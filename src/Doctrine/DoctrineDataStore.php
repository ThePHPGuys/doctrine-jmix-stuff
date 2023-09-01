<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Misterx\DoctrineJmix\Data\DataStore;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\SaveContext;

final class DoctrineDataStore extends DataStore\AbstractDataStore
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly QueryAssemblerFactory $queryAssemblerFactory)
    {

    }

    protected function loadAll(LoadContext $context): iterable
    {
        $this->createQuery($this->entityManager, $context, false);
    }

    protected function loadOne(LoadContext $context): ?object
    {
        // TODO: Implement loadOne() method.
    }

    protected function count(LoadContext $context): int
    {
        // TODO: Implement count() method.
    }

    protected function commit(SaveContext $context): mixed
    {
        // TODO: Implement commit() method.
    }

    private function createQuery(EntityManagerInterface $entityManager, LoadContext $context, bool $countQuery)
    {
        $contextQuery = $context->getQuery();
        $assembler = $this->queryAssemblerFactory->create();
        $assembler
            ->setId($context->getId())
            ->setIds($context->getIds())
            ->setEntityName($context->getMetaClass()->getName())
            ->setView($context->getView());


        if ($contextQuery) {
            //$assembler->setQueryString()
            $assembler
                ->setQueryString($contextQuery->getQueryString())
                ->setCondition($contextQuery->getCondition())
                ->setQueryParameters($contextQuery->getParameters());

            if ($contextQuery instanceof DoctrineContextQuery) {
                $assembler->setQueryBuilder($contextQuery->getQueryBuilder());
            }

            if (!$countQuery) {
                $assembler->setSort($contextQuery->getSort());
            } else {
                $assembler->setCountQuery();
            }
        }
        $query = $assembler->assembleQuery($entityManager);
        if ($contextQuery) {
            if ($contextQuery->getOffset() !== null) {
                $query->setFirstResult($contextQuery->getOffset());
            }
            if ($contextQuery->getLimit() !== null) {
                $query->setMaxResults($contextQuery->getLimit());
            }
        }
        dump('---------------DQL---------------');
        dump($query->getDQL());
        dump('---------------SQL---------------');
        dd($query->getSQL());
    }


}