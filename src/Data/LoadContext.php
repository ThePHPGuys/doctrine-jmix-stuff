<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

use Misterx\DoctrineJmix\Data\LoadContext\Query;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\Security\AccessConstraint;

final class LoadContext implements DataContext
{
    private ?Query $query = null;
    private ?View $view = null;
    /** @var AccessConstraint[] */
    private array $constraints = [];
    private string|int|null $id = null;

    /**
     * @var (int|string)[]
     */
    private array $ids = [];

    public function __construct(private readonly MetaClass $metaClass)
    {

    }

    public function getMetaClass(): MetaClass
    {
        return $this->metaClass;
    }

    /**
     * @return AccessConstraint[];
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @param AccessConstraint[] $constraints
     */
    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function getQuery(): ?Query
    {
        return $this->query;
    }

    public function setQuery(Query $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function getView(): ?View
    {
        return $this->view;
    }

    public function setView(View $view): self
    {
        $this->view = $view;
        return $this;
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return (string|int)[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function setIds(string|int ...$ids): self
    {
        $this->ids = array_values($ids);
        return $this;
    }
}