<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\LoadContext;

/**
 * @template T of object
 */
final class AfterLoadEvent implements DataStoreEvent
{
    /** @var array<T> */
    private array $excludedEntities = [];

    /**
     * @param iterable<T> $entities
     */
    public function __construct(private readonly LoadContext $loadContext, private readonly iterable $entities, private readonly EventSharedState $eventState)
    {
    }

    public function getLoadContext(): LoadContext
    {
        return $this->loadContext;
    }

    public function getEventState(): EventSharedState
    {
        return $this->eventState;
    }


    public function excludeEntity(object $excludedEntity): void
    {
        $this->excludedEntities[] = $excludedEntity;
    }

    /**
     * @return T|null
     */
    public function getResultEntity(): ?object
    {
        foreach ($this->getResultEntities() as $entity) {
            return $entity;
        }
        return null;
    }

    /**
     * @return iterable<T>
     */
    public function getResultEntities(): iterable
    {
        if (!$this->excludedEntities) {
            return $this->entities;
        }
        return array_filter($this->entities, fn(object $entity) => !in_array($entity, $this->excludedEntities));
    }
}