<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Hydrator\Filter;

use Laminas\Hydrator\Filter\FilterInterface;

final class DisableAllFilter implements FilterInterface
{
    public function filter(string $property, ?object $instance = null): bool
    {
        return false;
    }

}