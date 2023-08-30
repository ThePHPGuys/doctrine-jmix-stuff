<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel;

final class MetaClass extends MetaObject
{
    private array $properties = [];
    private string $className;
    private string $storeName;

    public function __construct(string $name, string $className = null)
    {
        parent::__construct($name);
        $this->className = $className ?? $name;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function registerProperty(MetaProperty $property): void
    {
        $this->properties[$property->getName()] = $property;
    }

    /**
     * @return MetaProperty[]
     */
    public function getProperties(): array
    {
        return array_values($this->properties);
    }

    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }

    public function getProperty(string $name): MetaProperty
    {
        return $this->properties[$name];
    }

    public function getPropertyPath(string $propertyPath): ?MetaPropertyPath
    {
        $properties = explode('.', $propertyPath);
        /** @var MetaProperty[] $metaProperties */
        $metaProperties = [];
        /** @var ?MetaProperty $currentProperty */
        $currentProperty = null;
        $currentClass = $this;

        foreach ($properties as $property) {
            if (!$currentClass) {
                return null;
            }
            if (!$currentClass->hasProperty($property)) {
                return null;
            }
            $currentProperty = $currentClass->getProperty($property);
            $range = $currentProperty->getRange();
            $currentClass = $range->isClass() ? $range->asClass() : null;
            $metaProperties[] = $currentProperty;
        }

        return new MetaPropertyPath($this, $metaProperties);
    }

    public function setStore(string $storeName): void
    {
        $this->storeName = $storeName;
    }

    public function getStore(): string
    {
        return $this->storeName;
    }
}