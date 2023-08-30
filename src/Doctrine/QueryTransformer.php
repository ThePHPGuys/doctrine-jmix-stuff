<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Direction;

interface QueryTransformer
{

    public const ALIAS_PLACEHOLDER = '{E}';

    public function addJoin(string $join, string $alias): void;

    public function addWhere(string $where): void;

    public function replaceWithCount(): void;

    public function getQuery(): Query;

    /**
     * @param array<string, Direction> $sortExpressions
     */
    public function replaceOrderByExpressions(array $sortExpressions): void;

}