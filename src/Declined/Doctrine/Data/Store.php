<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\DataStore\AbstractDataStore;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\SaveContext;

final class Store extends AbstractDataStore
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly QueryBuilderAssemblerFactory $assemblerFactory)
    {

    }

    private function getQueryBuilderFromQuery(LoadContext\Query $query):?QueryBuilder
    {
        if($query instanceof DoctrineORMLoadContextQuery){
            return $query->getBuilder();
        }
        return null;
    }

    protected function loadAll(LoadContext $context): iterable
    {

    }

    protected function loadOne(LoadContext $context): mixed
    {
        // TODO: Implement loadOne() method.
    }

    protected function count(LoadContext $context): mixed
    {
        // TODO: Implement count() method.
    }

    protected function commit(SaveContext $context): mixed
    {
        // TODO: Implement commit() method.
    }

    private function createQueryBuilder(LoadContext $context, bool $isCountQuery):QueryBuilder
    {
        $assembler = $this->assemblerFactory->create()
            ->setId($context->getId())
            ->setIds($context->getIds())
            ->setIsCountQuery($isCountQuery)
            ->setEntityClass($context->getMetaClass()->getClassName());

        if($contextQuery = $context->getQuery())
        {
            if($contextQuery instanceof DoctrineORMLoadContextQuery){
                $assembler->setQueryBuilder($contextQuery->getBuilder());
            }
            $assembler
                ->setSort($contextQuery->getSort())
                ->setCondition($contextQuery->getCondition());
        }
        if($fetchGroup = $context->getFetchGroup()){
            $assembler->setFetchGroup($fetchGroup);
        }
        $queryBuilder = $assembler->assemble($this->entityManager);
    }

    private function createFetchGroup(){

    }

}