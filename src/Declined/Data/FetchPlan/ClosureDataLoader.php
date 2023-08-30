<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

use Closure;

final class ClosureDataLoader implements DataLoader
{
    public function __construct(private readonly Closure $loader)
    {

    }
    public function load(mixed $keys): array
    {
        return ($this->loader)($keys);
    }

}