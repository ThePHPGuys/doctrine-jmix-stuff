<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\DataStore;
use Misterx\DoctrineJmix\Data\LoadContext;
use Misterx\DoctrineJmix\Data\SaveContext;

abstract class AbstractDataStore implements DataStore
{
    private ?DataStoreEventDispatcher $dispatcher = null;

    final public function setEventDispatcher(DataStoreEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @template T of DataStoreEvent
     * @param T $event
     * @return T
     */
    final protected function dispatch(DataStoreEvent $event): DataStoreEvent
    {
        if ($this->dispatcher) {
            return $this->dispatcher->dispatch($event);
        }
        return $event;
    }

    public function load(LoadContext $context): object|array|null
    {
        $loadState = new EventSharedState();
        $beforeEventResult = $this->dispatch(new BeforeLoadEvent($context, $loadState));

        if ($beforeEventResult->loadPrevented()) {
            return null;
        }

        $entity = $this->loadOne($context);

        $afterEventResult = $this->dispatch(new AfterLoadEvent($context, [$entity], $loadState));

        return $afterEventResult->getResultEntity();
    }


    public function loadList(LoadContext $context): iterable
    {
        $loadState = new EventSharedState();
        $beforeEventResult = $this->dispatch(new BeforeLoadEvent($context, $loadState));

        if ($beforeEventResult->loadPrevented()) {
            return [];
        }

        $entities = $this->loadAll($context);

        $afterEventResult = $this->dispatch(new AfterLoadEvent($context, $entities, $loadState));

        return $afterEventResult->getResultEntities();
    }

    public function getCount(LoadContext $context): int
    {
        $loadState = new EventSharedState();
        $beforeEventResult = $this->dispatch(new BeforeCountEvent($context, $loadState));

        if ($beforeEventResult->loadPrevented()) {
            return 0;
        }

        if (!$beforeEventResult->countByItems()) {
            return $this->count($context);
        }

        $countContext = clone $context;
        /**
         * TODO: Remove pagination from $countContext->getQuery();
         */
        $items = $this->loadAll($countContext);
        $afterEventResult = $this->dispatch(new AfterLoadEvent($context, $items, $loadState));
        return count($afterEventResult->getResultEntities());
    }

    public function save(SaveContext $context)
    {

    }


    abstract protected function loadAll(LoadContext $context): iterable;

    abstract protected function loadOne(LoadContext $context): object|array|null;

    abstract protected function count(LoadContext $context): int;

    abstract protected function commit(SaveContext $context): mixed;
}