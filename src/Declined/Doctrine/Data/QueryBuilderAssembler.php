<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Data\Sort;
use Misterx\DoctrineJmix\Declined\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaDataTools;

final class QueryBuilderAssembler
{

    private ?QueryBuilder $queryBuilder = null;
    private ?Sort $sort = null;
    private string|int|null $id = null;
    /**
     * @var array<int|string>|null
     */
    private array $ids = [];
    private bool $isCountQuery = false;
    private ?Condition $condition = null;
    private ?string $entityClass = null;
    private ?View $fetchGroup;

    public function __construct(private readonly MetaData $metaData, private readonly MetaDataTools $dataTools, private readonly QueryBuilderSortGenerator $sortGenerator)
    {

    }

    /**
     * Must be a class, not metaclass. It will be used in executor which has only entityclass
     * @param string $entityClass
     * @return $this
     */
    public function setEntityClass(string $entityClass):self
    {
        $this->entityClass = $entityClass;
        return $this;
    }
    public function setIsCountQuery(bool $isCountQuery):self
    {
        $this->isCountQuery = $isCountQuery;
        return $this;
    }



    public function setQueryBuilder(?QueryBuilder $queryBuilder):self
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    public function setSort(Sort $sort):self
    {
        $this->sort = $sort;
        return $this;
    }

    public function setId(int|string|null $id):self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param array<string|int>|null $ids
     * @return $this
     */
    public function setIds(array|null $ids):self
    {
        $this->ids = $ids;
        return $this;
    }

    public function setCondition(Condition $condition):self
    {
        $this->condition = $condition;
        return $this;
    }


    public function assemble(EntityManagerInterface $em):?QueryBuilder
    {
        return $this->buildResultQueryBuilder($em);
    }

    private function buildResultQueryBuilder(EntityManagerInterface $em):?QueryBuilder
    {

        if(!$this->queryBuilder && !$this->entityClass){
            return null;
        }

        $queryBuilder = $this->queryBuilder;

        if($this->entityClass){
            $metaClass = $this->metaData->getByClass($this->entityClass);

            if(!$queryBuilder){
                $queryBuilder = $this->createDefaultQueryBuilder($em,$metaClass);
            }

            if(!$this->isCountQuery) {
                $this->applySorting($metaClass,$queryBuilder);
            }

            $this->applyFilters($metaClass, $queryBuilder);
        }

        if($this->fetchGroup){
            //$queryBuilder->hint
        }
        return $queryBuilder;
    }

    private function createDefaultQueryBuilder(EntityManagerInterface $entityManager, MetaClass $metaClass):QueryBuilder
    {
        $queryBuilder = $entityManager->createQueryBuilder()
            ->select('e')->from($metaClass->getClassName(),'e');
        if($this->id){
            $queryBuilder->where(
                sprintf('e.%s = :entityId',$this->getPrimaryKeyName($metaClass))
            )->setParameter(':entityId',$this->id);
        }elseif ($this->ids){
            $queryBuilder->where(
                sprintf('e.%s IN :entityIds',$this->getPrimaryKeyName($metaClass))
            )->setParameter(':entityIds',$this->ids);
        }
        return $queryBuilder;
    }

    private function applySorting(MetaClass $metaClass, QueryBuilder $queryBuilder):void
    {
        print_r($this->sort);
        if($this->sort === null || !$this->sort->isSorted()){
            return;
        }
        $this->sortGenerator->addSort($metaClass,$queryBuilder,$this->sort);
    }

    private function applyFilters(MetaClass $metaClass, QueryBuilder $queryBuilder):void
    {

    }

    private function getPrimaryKeyName(MetaClass $metaClass):string
    {
        return $this->dataTools->getPrimaryKeyProperty($metaClass)->getName();
    }

    public function setFetchGroup(?View $fetchGroup):self
    {
        $this->fetchGroup = $fetchGroup;
        return $this;
    }
}