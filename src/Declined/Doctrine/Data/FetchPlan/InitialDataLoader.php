<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Data\FetchPlan;

use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Declined\Data\FetchPlan\DataLoader;

final class InitialDataLoader implements DataLoader
{
    public function __construct(private QueryBuilder $builder)
    {

    }

    public function load(mixed $keys): array
    {
        return $this->builder->getQuery()->execute();
    }

}