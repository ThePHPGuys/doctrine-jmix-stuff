<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

use ReflectionMethod;
use ReflectionProperty;

final class MetaProperty extends MetaObject
{
    private ?MetaProperty $inverse = null;
    private MetaPropertyType $type;
    private Range $range;
    private ReflectionProperty|ReflectionMethod $reflection;
    private bool $isRequired = true;
    private bool $isReadonly = false;
    private bool $isTransient = false;

    public function __construct(readonly private MetaClass $metaClass, string $name)
    {
        parent::__construct($name);
        $metaClass->registerProperty($this);

    }

    public function getInverse(): ?MetaProperty
    {
        return $this->inverse;
    }

    public function setInverse(MetaProperty $inverseProperty): void
    {
        $this->inverse = $inverseProperty;
    }

    public function getMetaClass(): MetaClass
    {
        return $this->metaClass;
    }

    public function setType(MetaPropertyType $type): void
    {
        $this->type = $type;
    }

    public function getType(): MetaPropertyType
    {
        return $this->type;
    }

    public function setRange(Range $range): void
    {
        $this->range = $range;
    }

    public function getRange(): Range
    {
        return $this->range;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }

    public function isReadonly(): bool
    {
        return $this->isReadonly;
    }

    public function setReadonly(bool $isReadonly): void
    {
        $this->isReadonly = $isReadonly;
    }

    public function setTransient(bool $isTransient)
    {
        $this->isTransient = $isTransient;
    }

    public function isTransient(): bool
    {
        return $this->isTransient;
    }


}