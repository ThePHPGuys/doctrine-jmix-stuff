<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use TPG\PMix\Data\SaveContext;

final class DoctrineSaveContext extends SaveContext
{
    private iterable $cascadeAffectedEntities = [];

    public function setCascadeAffectedEntities(object ...$entities): self
    {
        $this->cascadeAffectedEntities = $entities;
        return $this;
    }

    public function getCascadeAffectedEntities(): iterable
    {
        return $this->cascadeAffectedEntities;
    }

    function __construct(SaveContext $context)
    {
        $this->saving(...$context->getEntitiesToSave());
        $this->removing(...$context->getEntitiesToRemove());
        $this->setConstraints($context->getConstraints());
    }

}