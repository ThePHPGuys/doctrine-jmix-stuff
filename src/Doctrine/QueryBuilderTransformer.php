<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Misterx\DoctrineJmix\Data\Direction;

final class QueryBuilderTransformer implements QueryTransformer
{
    private QueryBuilder $queryBuilder;
    private string $rootAlias;

    public function __construct(QueryBuilder $baseBuilder)
    {
        $this->queryBuilder = clone $baseBuilder;
        $this->rootAlias = $this->queryBuilder->getRootAliases()[0];
    }

    public function addJoin(string $join, string $alias): void
    {
        $this->queryBuilder->join($this->replaceEntityPlaceholder($join, $this->getRootAlias()), $alias);
    }

    public function addWhere(string $where): void
    {
        $this->queryBuilder->andWhere($this->replaceEntityPlaceholder($where, $this->getRootAlias()));
    }

    public function replaceWithCount(): void
    {

    }

    public function getQuery(): Query
    {
        return $this->queryBuilder->getQuery();
    }

    /**
     * @param array<string, Direction> $sortExpressions
     * @return void
     */
    public function replaceOrderByExpressions(array $sortExpressions): void
    {
        $orderByExpression = new Query\Expr\OrderBy();
        foreach ($sortExpressions as $sort => $order) {
            $orderByExpression->add($sort, $order->value);
        }
        $this->queryBuilder->orderBy($orderByExpression);
    }

    private function replaceEntityPlaceholder(string $value, string $entityName): string
    {
        if (str_contains($value, QueryTransformer::ALIAS_PLACEHOLDER)) {
            return str_replace(QueryTransformer::ALIAS_PLACEHOLDER, $entityName, $value);
        }
        return $value;
    }

    private function getRootAlias(): string
    {
        return $this->rootAlias;
    }
}