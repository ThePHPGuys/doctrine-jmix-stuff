<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\LoadContext;

final class BeforeLoadEvent implements DataStoreEvent
{
    private bool $loadPrevented = false;

    public function __construct(readonly public LoadContext $loadContext, readonly public EventSharedState $eventState)
    {
    }

    public function preventLoad(): void
    {
        $this->loadPrevented = true;
    }

    public function loadPrevented(): bool
    {
        return $this->loadPrevented;
    }
}