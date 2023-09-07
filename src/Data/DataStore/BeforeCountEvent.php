<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\LoadContext;

final class BeforeCountEvent implements DataStoreEvent
{

    private bool $countPrevented = false;
    private bool $countByItems = false;

    public function __construct(private readonly LoadContext $loadContext, private readonly EventSharedState $eventState)
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

    public function preventCount(): void
    {
        $this->countPrevented = true;
    }

    public function countPrevented(): bool
    {
        return $this->countPrevented;
    }

    public function setCountByItems(): void
    {
        $this->countByItems = true;
    }

    public function countByItems(): bool
    {
        return $this->countByItems;
    }
}