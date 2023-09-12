<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

final class MetaPropertyPath
{
    private array $path = [];
    private string $pathString;

    /**
     * @param MetaProperty[] $metaProperties
     */
    public function __construct(private readonly MetaClass $metaClass, private readonly array $metaProperties)
    {
        array_map(fn(MetaProperty $property) => $this->path[] = $property->getName(), $this->metaProperties);
        $this->pathString = implode('.', $this->path);
    }

    public function getMetaClass(): MetaClass
    {
        return $this->metaClass;
    }

    /**
     * @return MetaProperty[]
     */
    public function getMetaProperties(): array
    {
        return $this->metaProperties;
    }

    public function getMetaProperty(): MetaProperty
    {
        return $this->getLastProperty();
    }

    private function getLastProperty(): MetaProperty
    {
        return $this->metaProperties[array_key_last($this->metaProperties)];
    }

    public function isDirectProperty(): bool
    {
        return count($this->metaProperties) === 1;
    }

    public function getPathString(): string
    {
        return $this->pathString;
    }

    public static function append(MetaPropertyPath $parentPath, MetaProperty ...$property): self
    {
        return new self($parentPath->getMetaClass(), [...$parentPath->getMetaProperties(), ...$property]);
    }

}