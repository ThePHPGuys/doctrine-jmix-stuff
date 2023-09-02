<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Data\LoadContext\ParameterValue;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGenerationContext;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\QueryParamValuesManager;

final class QueryAssembler
{
    private string|int|null $id = null;

    private string $entityName;
    /**
     * @var int[]|string[]
     */
    private array $ids = [];

    private ?Condition $condition = null;
    private ?string $queryString = null;
    /**
     * @var array<string,mixed>
     */
    private array $parameters = [];

    /**
     * @var array<string,mixed>
     */
    private array $resultParameters = [];

    private ?Sort $sort = null;
    private ?Query $resultQuery = null;
    private ?View $view = null;
    private bool $countQuery = false;
    private ?QueryBuilder $queryBuilder = null;
    /**
     * @var callable(QueryTransformer):void
     */
    private $onBuildHandler;
    private array $resultParametersFromProvider = [];

    public function __construct(
        private readonly MetaData                          $metaData,
        private readonly MetaDataTools                     $metaDataTools,
        private readonly QuerySortProcessor                $sortProcessor,
        private readonly QueryParamValuesManager           $paramValuesProvider,
        private readonly QueryConditionProcessor           $queryConditionProcessor,
        private readonly QueryConditionParametersProcessor $queryConditionParametersProcessor,
        private readonly QueryViewProcessor                $queryViewProcessor,
    )
    {

    }


    public function setId(string|int|null $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param list<string|int> $ids
     * @return $this
     */
    public function setIds(array $ids): self
    {
        $this->ids = $ids;
        return $this;
    }

    public function setEntityName(string $name): self
    {
        $this->entityName = $name;
        return $this;
    }

    public function setCondition(?Condition $condition): self
    {
        $this->condition = $condition;
        return $this;
    }

    public function setQueryString(?string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * @param array<string,mixed> $parameters
     * @return $this
     */
    public function setQueryParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function setSort(?Sort $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function setCountQuery(): self
    {
        $this->countQuery = true;
        return $this;
    }

    public function setView(?View $view): self
    {
        $this->view = $view;
        return $this;
    }

    public function setQueryBuilder(?QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function assembleQuery(EntityManagerInterface $entityManager): Query
    {
        $query = $this->getResultQuery($entityManager);


        $allParameters = $this->resultParameters;

        foreach ($this->resultParametersFromProvider as $parameter) {
            $allParameters[$parameter] = $this->paramValuesProvider->getValue($parameter);
        }

        foreach ($allParameters as $parameterName => $parameterValue) {
            if ($parameterValue instanceof ParameterValue) {
                $query->setParameter($parameterName, $parameterValue->getValue(), $parameterValue->isTypeWasSpecified() ? $parameterValue->getType() : null);
            } else {
                $query->setParameter($parameterName, $parameterValue);
            }
        }
        return $query;
    }

    private function getResultQuery(EntityManagerInterface $em): Query
    {
        if (!$this->resultQuery) {
            $this->resultQuery = $this->buildResultQuery($em);
        }
        return $this->resultQuery;
    }

    /**
     * @param callable(QueryTransformer):void $onBuildHandler
     * @return void
     */
    public function onBuild(callable $onBuildHandler): void
    {
        $this->onBuildHandler = $onBuildHandler;
    }

    private function buildResultQuery(EntityManagerInterface $em): Query
    {
        $this->resultParameters = $this->parameters;
        if ($this->queryString) {
            return $em->createQuery($this->queryString);
        }

        if (!$this->entityName) {
            throw new \InvalidArgumentException('EntityName must be provided');
        }

        $queryBuilder = $this->queryBuilder ?? $this->createDefaultQueryBuilder($em);
        $queryTransformer = new QueryBuilderTransformer($queryBuilder);
        $this->applySorting($queryTransformer);
        $this->applyFiltering($queryTransformer);
        $this->applyView($queryTransformer);
        $this->applyCount($queryTransformer);
        if ($this->onBuildHandler) {
            ($this->onBuildHandler)($queryTransformer);
        }

        return $queryTransformer->getQuery();
    }

    private function createDefaultQueryBuilder(EntityManagerInterface $em): QueryBuilder
    {
        $queryBuilder = $em->createQueryBuilder();
        $metaClass = $this->metaData->getByName($this->entityName);
        $queryBuilder->select('e')->from($metaClass->getClassName(), 'e');

        if ($this->id) {
            $queryBuilder
                ->andWhere(sprintf('e.%s = :entityId', $this->metaDataTools->getPrimaryKeyPropertyName($metaClass)));
            $this->resultParameters['entityId'] = $this->id;
        } elseif ($this->ids) {
            $queryBuilder
                ->andWhere(sprintf('e.%s IN :entityIds', $this->metaDataTools->getPrimaryKeyPropertyName($metaClass)));
            $this->resultParameters['entityIds'] = $this->ids;
        }
        return $queryBuilder;
    }

    private function applySorting(QueryTransformer $queryTransformer): void
    {
        if (!$this->sort || !$this->sort->isSorted()) {
            return;
        }
        $this->sortProcessor->process($queryTransformer, $this->sort, $this->entityName);
    }

    private function applyFiltering(QueryTransformer $queryTransformer): void
    {
        if (!$this->condition) {
            return;
        }
        //Provided parameters
        $providedParameters = array_keys($this->parameters);
        $parametersFromProvider = [];
        //$this->condition->getParameters() - Required parameters by conditions
        foreach ($this->condition->getParameters() as $parameter) {
            if (!$this->paramValuesProvider->supports($parameter)) {
                continue;
            }
            $providedParameters[] = $parameter;
            $parametersFromProvider[] = $parameter;
        }

        $actualizedConditions = $this->condition->actualize($providedParameters);

        if (!$actualizedConditions) {
            //We have no conditions
            return;
        }
        $this->resultParametersFromProvider = $parametersFromProvider;
        $this->resultParameters = $this->queryConditionParametersProcessor->process(
            $this->resultParameters,
            $this->parameters,
            $actualizedConditions
        );

        $conditionGenerationContext = new ConditionGenerationContext($actualizedConditions);
        $conditionGenerationContext->setEntityName($this->entityName);
        $this->queryConditionProcessor->process($queryTransformer, $conditionGenerationContext);
    }

    private function applyView(QueryTransformer $queryTransformer): void
    {
        if (!$this->view) {
            return;
        }
        $this->queryViewProcessor->process($queryTransformer, $this->view, $this->entityName);
    }

    private function applyCount(QueryBuilderTransformer $queryTransformer): void
    {
        if ($this->countQuery) {
            $queryTransformer->replaceWithCount();
        }
    }

}