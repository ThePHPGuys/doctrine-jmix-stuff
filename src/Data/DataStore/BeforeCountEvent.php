<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

use Misterx\DoctrineJmix\Data\LoadContext;

final class BeforeCountEvent
{

    private bool $countPrevented = false;
    private bool $countByItems = false;

    /**
     * @param LoadContext $context
     * @param EventSharedState $loadState
     */
    public function __construct(public readonly LoadContext $context, public readonly EventSharedState $loadState)
    {

    }

    public function preventCount(): void
    {
        $this->countPrevented = true;
    }

    public function countPrevented(): bool
    {
        return $this->countPrevented;
    }

    public function setCountByItems(): bool
    {
        $this->countByItems = true;
    }

    public function countByItems(): bool
    {
        return $this->countByItems;
    }
}