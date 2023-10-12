<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Serializer\Hydrator\Strategy;

use Doctrine\Laminas\Hydrator\Strategy\CollectionStrategyInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Laminas\Hydrator\Strategy\CollectionStrategy;

final class CollectionStrategyExtractor extends CollectionStrategy implements CollectionStrategyInterface
{
    private ClassMetadata $classMetadata;
    private string $collectionName;
    private object $object;

    public function setCollectionName(string $collectionName): void
    {
        $this->collectionName = $collectionName;
    }

    public function getCollectionName(): string
    {
        return $this->collectionName;
    }

    public function setClassMetadata(ClassMetadata $classMetadata): void
    {
        $this->classMetadata = $classMetadata;
    }

    public function getClassMetadata(): ClassMetadata
    {
        return $this->classMetadata;
    }

    public function setObject(object $object): void
    {
        $this->object = $object;
    }

    public function getObject(): object
    {
        return $this->object;
    }
}
