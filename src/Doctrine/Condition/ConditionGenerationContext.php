<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Condition;

use TPG\PMix\Data\Condition;
use SplObjectStorage;

final class ConditionGenerationContext
{
    /** @var SplObjectStorage<Condition,self> */
    private SplObjectStorage $childContexts;

    private ?string $entityName = null;
    private ?string $entityAlias = null;
    private ?string $joinAlias = null;
    private ?string $joinProperty = null;
    private ?string $joinEntity = null;

    public function __construct(private readonly Condition $condition)
    {
        $this->childContexts = new SplObjectStorage();
        if ($this->condition instanceof Condition\LogicalCondition) {
            $this->createChildContexts($this->condition);
        }
    }

    private function createChildContexts(Condition\LogicalCondition $condition): void
    {
        foreach ($condition->getConditions() as $condition) {
            $this->childContexts[$condition] = new self($condition);
        }
    }

    public function getCondition(): Condition
    {
        return $this->condition;
    }

    /**
     * @return SplObjectStorage<Condition,self>
     */
    public function getChildContexts(): SplObjectStorage
    {
        return $this->childContexts;
    }

    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    public function setEntityName(?string $entityName): void
    {
        $this->entityName = $entityName;
    }

    public function getEntityAlias(): ?string
    {
        return $this->entityAlias;
    }

    public function setEntityAlias(?string $entityAlias): void
    {
        $this->entityAlias = $entityAlias;
    }

    public function getJoinAlias(): ?string
    {
        return $this->joinAlias;
    }

    public function setJoinAlias(?string $joinAlias): void
    {
        $this->joinAlias = $joinAlias;
    }

    public function getJoinEntity(): ?string
    {
        return $this->joinEntity;
    }

    public function setJoinEntity(?string $joinEntity): void
    {
        $this->joinEntity = $joinEntity;
    }

    public function getJoinProperty(): ?string
    {
        return $this->joinProperty;
    }

    public function setJoinProperty(?string $joinProperty): void
    {
        $this->joinProperty = $joinProperty;
    }

    public function copyEntityValuesToChildContexts(): void
    {
        foreach ($this->childContexts as $condition) {
            $childContext = $this->childContexts[$condition];
            $childContext->setEntityName($this->getEntityName());
            $childContext->setEntityAlias($this->getEntityAlias());
            $childContext->copyEntityValuesToChildContexts();
        }
    }

}