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
    public function __construct(public readonly LoadContext $context, private readonly iterable $entities, public readonly EventSharedState $loadState)
    {
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