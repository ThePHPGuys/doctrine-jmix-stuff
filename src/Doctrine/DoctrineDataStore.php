<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Misterx\DoctrineJmix\Data\DataStore;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\SaveContext;
use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\Security\AccessManager;
use Misterx\DoctrineJmix\Security\ReadEntityQueryContext;
use Misterx\DoctrineJmix\ViewsRepository;

final class DoctrineDataStore extends DataStore\AbstractDataStore
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly QueryAssemblerFactory  $queryAssemblerFactory,
        private readonly ViewsRepository        $viewsRepository,
        private readonly AccessManager          $accessManager
    )
    {

    }

    protected function loadAll(LoadContext $context): iterable
    {
        return $this->createQuery($this->entityManager, $context, false)->execute();
    }

    protected function loadOne(LoadContext $context): object|array|null
    {
        $result = $this->createQuery($this->entityManager, $context, false)->getOneOrNullResult();
        assert(is_object($result) || is_array($result) || is_null($result));
        return $result;
    }

    protected function count(LoadContext $context): int
    {
        return (int)$this->createQuery($this->entityManager, $context, true)->getScalarResult();
    }

    private function createQuery(EntityManagerInterface $entityManager, LoadContext $context, bool $countQuery): Query
    {
        $contextQuery = $context->getQuery();
        $assembler = $this->queryAssemblerFactory->create();
        $assembler
            ->setId($context->getId())
            ->setIds($context->getIds())
            ->setEntityName($context->getMetaClass()->getName());


        if ($contextQuery) {
            if ($contextQuery instanceof DoctrineContextQuery) {
                $assembler->setQueryBuilder($contextQuery->getQueryBuilder());
            }
            $assembler
                ->setQueryString($contextQuery->getQueryString())
                ->setCondition($contextQuery->getCondition())
                ->setQueryParameters($contextQuery->getParameters());

            if (!$countQuery) {
                $assembler->setSort($contextQuery->getSort());
            }
        }

        if ($countQuery) {
            $assembler->setCountQuery();
        } else {
            $assembler->setView($this->createView($context));
        }

        $assembler->onBuild(
            fn(QueryTransformer $transformer) => $this->accessManager
                ->applyConstraints(
                    new ReadEntityQueryContext($context->getMetaClass(), $transformer), $context->getConstraints()
                )
        );

        $query = $assembler->assembleQuery($entityManager);
        if ($contextQuery) {
            if ($contextQuery->getOffset() !== null) {
                $query->setFirstResult($contextQuery->getOffset());
            }
            if ($contextQuery->getLimit() !== null) {
                $query->setMaxResults($contextQuery->getLimit());
            }
        }
        return $query;
    }

    private function createView(LoadContext $context): View
    {
        return $context->getView() ?? $this->viewsRepository->getMetaClassView($context->getMetaClass(), View::BASE);
    }

    protected function saveAll(SaveContext $context): iterable
    {
        $result = [];
        foreach ($context->getEntitiesToSave() as $entity) {
            if (!$this->entityManager->contains($entity)) {
                $this->entityManager->persist($entity);
            }
            $result[] = $entity;
        }
        return $result;
    }

    protected function removeAll(SaveContext $context): iterable
    {
        $result = [];
        foreach ($context->getEntitiesToRemove() as $entity) {
            $this->entityManager->remove($entity);
            $result[] = $entity;
        }

        return $result;
    }

    protected function beforeSaveCommit(SaveContext $context, iterable $savedEntities, iterable $deletedEntities): void
    {
        //This method can be used if dispatch final changing event needed
    }

    protected function commitSave(): void
    {
        $this->entityManager->flush();
    }

}