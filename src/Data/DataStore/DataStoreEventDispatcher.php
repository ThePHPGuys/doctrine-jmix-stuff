<?php
declare(strict_types=1);

namespace TPG\PMix\Data\DataStore;

interface DataStoreEventDispatcher
{
    /**
     * @template T of DataStoreEvent
     * @param T $event
     * @return T The Event that was passed, now modified by listeners.
     */
    public function dispatch(DataStoreEvent $event): DataStoreEvent;
}