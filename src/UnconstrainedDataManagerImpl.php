<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\DataStore;
use Misterx\DoctrineJmix\Data\DataStores;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\SaveContext;
use Misterx\DoctrineJmix\Data\UnconstrainedDataManager;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\Security\AccessConstraint;

final class UnconstrainedDataManagerImpl implements UnconstrainedDataManager
{

    public function __construct(private readonly DataStores $dataStores)
    {

    }

    public function load(LoadContext $context): ?object
    {
        $clonedContext = clone $context;
        $metaClass = $context->getMetaClass();
        $dataStore = $this->getDataStore($metaClass);
        $clonedContext->setConstraints($this->mergeConstraints($clonedContext->getConstraints()));
        $entity = $dataStore->load($clonedContext);
        /**
         * if (entity != null)
         * readCrossDataStoreReferences(Collections.singletonList(entity), context.getFetchPlan(), metaClass, context.isJoinTransaction());
         */
        return $entity;
    }

    public function loadList(LoadContext $context): iterable
    {
        $clonedContext = clone $context;
        $metaClass = $context->getMetaClass();
        $dataStore = $this->getDataStore($metaClass);
        $clonedContext->setConstraints($this->mergeConstraints($clonedContext->getConstraints()));
        $entities = $dataStore->loadList($clonedContext);
        // readCrossDataStoreReferences(entities, context.getFetchPlan(), metaClass, context.isJoinTransaction());
        return $entities;
    }

    public function getCount(LoadContext $context): int
    {
        // TODO: Implement getCount() method.
    }

    public function save(SaveContext $context)
    {
        // TODO: Implement save() method.
    }

    public function remove(SaveContext $context)
    {
        // TODO: Implement remove() method.
    }

    private function getDataStore(MetaClass $class): DataStore
    {
        return $this->dataStores->get($class->getStore());
    }

    /**
     * @param AccessConstraint[] $constraints
     * @return AccessConstraint[]
     */
    protected function mergeConstraints(array $constraints): array
    {
        if (!$constraints) {
            return $this->getAppliedConstraints();
        }
        return [...$this->getAppliedConstraints(), ...$constraints];
    }

    /**
     * @return AccessConstraint[]
     */
    protected function getAppliedConstraints(): array
    {
        return [];
    }
}