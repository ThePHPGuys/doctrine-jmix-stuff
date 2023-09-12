<?php
declare(strict_types=1);

namespace TPG\PMix\Data\DataStore;

use TPG\PMix\Data\SaveContext;

final class BeforeSaveEvent implements DataStoreEvent
{
    private bool $savePrevented = false;

    public function __construct(private SaveContext $saveContext, private EventSharedState $eventState)
    {

    }


    public function getSaveContext(): SaveContext
    {
        return $this->saveContext;
    }

    public function getEventState(): EventSharedState
    {
        return $this->eventState;
    }

    public function preventSave(): void
    {
        $this->savePrevented = true;
    }

    public function savePrevented(): bool
    {
        return $this->savePrevented;
    }
}