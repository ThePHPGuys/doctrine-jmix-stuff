<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Data\LoadContext\Parameter;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGenerationContext;
use Misterx\DoctrineJmix\Doctrine\Condition\ConditionGeneratorResolver;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\QueryParamValuesProvider;

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
     * @var Parameter[]
     */
    private array $parameters = [];
    private ?Sort $sort = null;
    private ?Query $resultQuery = null;
    private ?View $view = null;

    public function __construct(
        private readonly MetaData                 $metaData,
        private readonly MetaDataTools            $metaDataTools,
        private readonly QuerySortProcessor       $sortProcessor,
        private readonly QueryParamValuesProvider $paramValuesProvider,
        private readonly QueryConditionProcessor  $queryConditionProcessor
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
     * @param Parameter[] $parameters
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

    public function assembleQuery(EntityManagerInterface $entityManager): Query
    {
        return $this->getResultQuery($entityManager);
    }

    private function getResultQuery(EntityManagerInterface $em): Query
    {
        if (!$this->resultQuery) {
            $this->resultQuery = $this->buildResultQuery($em);
        }
        return $this->resultQuery;
    }

    private function buildResultQuery(EntityManagerInterface $em): Query
    {
        //Може э зміст створювати query builder і з ним працювати, чим працювати з DQL
        //Тобто якщо вказана queryString то нічого не робити, бо ми по факту нічого і не зможем зробити
        //Хіба перевірити якщо починається на WHERE то просто додати до умов
        //Для фільтрів дивитись io.jmix.data.impl.jpql.generator.ConditionJpqlGenerator#processQuery
        //query_conditions/QueryConditionsTest.groovy:115
        //Ще варіант такий, якщо в запиті присутньо Where то не додавати умови, інакше додати умови
        //Або не дозволяти з where запити, питання з join???
        if ($this->queryString) {
            return $em->createQuery($this->queryString);
        }

        if (!$this->entityName) {
            throw new \InvalidArgumentException('EntityName must be provided');
        }

        $queryBuilder = $em->createQueryBuilder();
        $metaClass = $this->metaData->getByName($this->entityName);
        $queryBuilder->select('e')->from($metaClass->getClassName(), 'e');

        if ($this->id) {
            $queryBuilder
                ->andWhere(sprintf('e.%s = :entityId', $this->metaDataTools->getPrimaryKeyPropertyName($metaClass)));
            $this->parameters[] = new Parameter('entityId', $this->id);
        } elseif ($this->ids) {
            $queryBuilder
                ->andWhere(sprintf('e.%s IN :entityIds', $this->metaDataTools->getPrimaryKeyPropertyName($metaClass)));
            $this->parameters[] = new Parameter('entityIds', $this->ids);
        }
        $queryTransformer = new QueryBuilderTransformer($queryBuilder);
        $this->applySorting($queryTransformer);
        $this->applyFiltering($queryTransformer);
        $this->applyView($queryTransformer);
        return $queryTransformer->getQuery();
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

        //$this->condition->getParameters() - Required parameters by conditions

        foreach ($this->condition->getParameters() as $parameter) {
            if (!$this->paramValuesProvider->supports($parameter)) {
                continue;
            }
            $providedParameters[] = $parameter;
        }

        $actualizedConditions = $this->condition->actualize($providedParameters);

        if (!$actualizedConditions) {
            //We have no conditions
            return;
        }
        //If we have actual conditions, we need to feel parameters with values that was provided by provider
        //TODO: Generate parameters values

        $conditionGenerationContext = new ConditionGenerationContext($actualizedConditions);
        $conditionGenerationContext->setEntityName($this->entityName);
        $this->queryConditionProcessor->process($queryTransformer, $conditionGenerationContext);
    }

    private function applyView(QueryTransformer $queryTransformer): void
    {
        if (!$this->view) {
            return;
        }
    }


}