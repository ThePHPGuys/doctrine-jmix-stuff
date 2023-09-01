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
        if (in_array($alias, $this->getJoinAliases())) {
            return;
        }
        $this->queryBuilder->join($this->replaceEntityPlaceholder($join, $this->getRootAlias()), $alias);

    }

    private function getJoinAliases(): array
    {
        $joins = $this->queryBuilder->getDQLPart('join');
        if (!$joins) {
            return [];
        }
        if (!array_key_exists($this->getRootAlias(), $joins)) {
            return [];
        }
        return array_map(fn(Query\Expr\Join $join) => $join->getAlias(), $joins[$this->getRootAlias()]);
    }

    public function addWhere(string $where): void
    {
        $this->queryBuilder->andWhere($this->replaceEntityPlaceholder($where, $this->getRootAlias()));
    }

    public function replaceWithCount(): void
    {
        $this->queryBuilder->select(sprintf('COUNT(%s)', $this->getRootAlias()));
        $this->queryBuilder->resetDQLParts('orderBy');
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
            $orderByExpression->add($this->replaceEntityPlaceholder($sort, $this->getRootAlias()), $order->value);
        }
        $this->queryBuilder->orderBy($orderByExpression);
    }

    public function replaceSelect(array $select): void
    {
        $selectList = array_map(fn(string $select) => $this->replaceEntityPlaceholder($select, $this->getRootAlias()), $select);
        $this->queryBuilder->select($selectList);
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