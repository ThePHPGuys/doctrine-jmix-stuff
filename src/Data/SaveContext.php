<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

use Misterx\DoctrineJmix\Security\AccessConstraint;

class SaveContext implements DataContext
{
    private array $constraints = [];

    private array $entitiesToSave = [];
    private array $entitiesToRemove = [];

    public function saving(object ...$entities): self
    {
        $this->entitiesToSave = [...$this->entitiesToSave, ...$entities];
        return $this;
    }

    public function removing(object ...$entities): self
    {
        $this->entitiesToRemove = [...$this->entitiesToRemove, ...$entities];
        return $this;
    }

    public function getEntitiesToSave(): array
    {
        return $this->entitiesToSave;
    }

    public function getEntitiesToRemove(): array
    {
        return $this->entitiesToRemove;
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
}