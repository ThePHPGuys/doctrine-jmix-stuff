<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel;

final class MetaData
{
    private array $byName = [];
    private array $byClass = [];

    public function getByName(string $name): MetaClass
    {
        return $this->findByName($name);
    }

    public function getByClass(string $class): MetaClass
    {
        return $this->findByClass($class);
    }

    public function getByObject(object $object): MetaClass
    {
        return $this->findByClass(get_class($object));
    }

    public function findByName(string $name): ?MetaClass
    {
        return array_key_exists($name, $this->byName) ? $this->byName[$name] : null;
    }

    public function findByClass(string $class): ?MetaClass
    {
        return array_key_exists($class, $this->byClass) ? $this->byClass[$class] : null;
    }

    public function register(MetaClass $metaClass): void
    {
        $this->byName[$metaClass->getName()] = $metaClass;
        $this->byClass[$metaClass->getClassName()] = $metaClass;
    }

    /**
     * @return MetaClass[]
     */
    public function getClasses(): array
    {
        return array_values($this->byClass);
    }
}