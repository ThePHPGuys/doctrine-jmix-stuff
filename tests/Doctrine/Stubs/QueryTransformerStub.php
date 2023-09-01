<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Doctrine\Stubs;

use Doctrine\ORM\Query;
use Misterx\DoctrineJmix\Data\Direction;
use Misterx\DoctrineJmix\Doctrine\QueryTransformer;

final class QueryTransformerStub implements QueryTransformer
{
    private array $joins = [];
    private array $where = [];
    private array $select = [];
    private bool $isCountQuery = false;
    /**
     * @var array<string,Direction>
     */
    private array $orderBy = [];

    public function addJoin(string $join, string $alias): void
    {
        $this->joins[$alias] = $join;
    }

    public function addWhere(string $where): void
    {
        $this->where[] = $where;
    }

    public function replaceWithCount(): void
    {
        $this->isCountQuery = true;
    }

    public function getQuery(): Query
    {
        throw new \Exception('It is stub!');
    }

    public function replaceOrderByExpressions(array $sortExpressions): void
    {
        $this->orderBy = $sortExpressions;
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    public function getWhereString(): string
    {
        return implode(' AND ', $this->where);
    }

    /**
     * @return bool
     */
    public function isCountQuery(): bool
    {
        return $this->isCountQuery;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return array_map(fn(Direction $o) => $o->value, $this->orderBy);
    }

    public function replaceSelect(array $select): void
    {
        $this->select = $select;
    }


    public function getSelect(): array
    {
        return $this->select;
    }


}