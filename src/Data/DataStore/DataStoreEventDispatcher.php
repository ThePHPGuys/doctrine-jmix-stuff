<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

interface DataStoreEventDispatcher
{
    /**
     * @template T of DataStoreEvent
     * @param T $event
     * @return DataStoreEvent The Event that was passed, now modified by listeners.
     */
    public function dispatch(DataStoreEvent $event): DataStoreEvent;
}