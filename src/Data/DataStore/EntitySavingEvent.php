<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\SaveContext;

final class EntitySavingEvent implements DataStoreEvent
{

    public function __construct(private readonly SaveContext $saveContext, private readonly iterable $entities, private readonly EventSharedState $eventState)
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

    public function getEntities(): iterable
    {
        return $this->entities;
    }
}