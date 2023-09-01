<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

final class View
{
    /** @var string Includes base and instanceName */
    public const BASE = '_base';
    /** @var string Includes all local properties. */
    public const LOCAL = '_local';
    /** @var string Includes only properties contained in InstanceName */
    public const INSTANCE_NAME = '_instance_name';
    /**
     * @var array<string,ViewProperty>
     */
    private array $properties = [];

    /**
     * @param ViewProperty[] $properties
     */
    public function __construct(array $properties = [], public readonly ?string $name = null)
    {
        foreach ($properties as $property) {
            assert($property instanceof ViewProperty);
            $this->properties[$property->name] = $property;
        }
    }

    /**
     * @return array<string,ViewProperty>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }

    public function getProperty(string $name): ?ViewProperty
    {
        return $this->properties[$name] ?? null;
    }
}