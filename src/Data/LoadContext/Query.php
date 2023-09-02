<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\LoadContext;

use Misterx\DoctrineJmix\Data\Condition;
use Misterx\DoctrineJmix\Data\Sort;


class Query
{
    /**
     * @var array<string,mixed>
     */
    private array $parameters = [];

    private ?int $offset = null;
    private ?int $limit = null;
    private ?Sort $sort = null;
    private ?Condition $condition = null;

    public function __construct(private ?string $queryString = null)
    {

    }


    public function setParameter(string $name, mixed $value): self
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setSort(Sort $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    public function getSort(): Sort
    {
        return $this->sort ?: Sort::unsorted();
    }

    public function setCondition(Condition $condition): self
    {
        $this->condition = $condition;
        return $this;
    }

    public function getCondition(): ?Condition
    {
        return $this->condition;
    }

    public function getQueryString(): ?string
    {
        return $this->queryString;
    }

    public function setQueryString(?string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    public static function create(): self
    {
        return new self();
    }
}