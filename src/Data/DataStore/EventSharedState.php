<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\DataStore;

final class EventSharedState
{
    /** @var array<string, mixed> */
    private array $values = [];

    public function set(string $key, mixed $value): void
    {
        $this->values[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }
}