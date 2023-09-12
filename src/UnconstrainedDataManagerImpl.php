<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\Data\DataStore;
use TPG\PMix\Data\DataStores;
use TPG\PMix\Data\LoadContext;
use TPG\PMix\Data\SaveContext;
use TPG\PMix\Data\UnconstrainedDataManager;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Security\AccessConstraint;

final class UnconstrainedDataManagerImpl implements UnconstrainedDataManager
{

    public function __construct(private readonly DataStores $dataStores, private readonly MetaData $metaData)
    {

    }

    public function load(LoadContext $context): object|array|null
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
        //TODO: Fix me
        return 0;
    }

    public function save(SaveContext $context)
    {
        $clonedContext = clone $context;
        $clonedContext->setConstraints($this->mergeConstraints($clonedContext->getConstraints()));
        /** @var array<string,SaveContext> $storeToContext */
        $storeToContext = [];
        //Create aggregated contexts by storeName
        foreach ($clonedContext->getEntitiesToSave() as $entity) {
            $metadata = $this->metaData->getByObject($entity);
            $this->getOrCreateSaveContext($storeToContext, $metadata->getStore(), $clonedContext)->saving($entity);
        }
        foreach ($clonedContext->getEntitiesToRemove() as $entity) {
            $metadata = $this->metaData->getByObject($entity);
            $this->getOrCreateSaveContext($storeToContext, $metadata->getStore(), $clonedContext)->removing($entity);
        }

        foreach ($storeToContext as $storeName => $context) {
            $this->saveContextToStore($storeName, $context);
        }
    }

    /**
     * @param array<string,SaveContext> $contexts
     * @return SaveContext
     */
    private function getOrCreateSaveContext(array &$contexts, string $storeName, SaveContext $saveContext): SaveContext
    {
        if (!array_key_exists($storeName, $contexts)) {
            return $contexts[$storeName] = $this->createSaveContext($saveContext);
        }
        return $contexts[$storeName];
    }

    private function createSaveContext(SaveContext $saveContext): SaveContext
    {
        return (new SaveContext())->setConstraints($saveContext->getConstraints());
    }

    private function saveContextToStore(string $storeName, SaveContext $saveContext)
    {
        $this->dataStores->get($storeName)->save($saveContext);
    }

    public function removeEntity(object ...$entities)
    {
        $this->save((new SaveContext())->removing(...$entities));
    }

    public function saveEntity(object ...$entities)
    {
        $this->save((new SaveContext())->saving(...$entities));
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