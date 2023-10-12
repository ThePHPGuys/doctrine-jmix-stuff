<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Serializer;

use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Persistence\ObjectManager;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\MetaData;

final class Hydrator
{
    public function __construct(private ObjectManager $objectManager, private MetaData $metaData)
    {

    }

    private function resolveObject(string|object $object)
    {
        if (is_string($object)) {
            return new $object;
        } else {
            return $object;
        }
    }

    private function hydrateOne(array $values, MetaClass $metaClass, string|object $object): object
    {
        $preparedData = [];
        foreach ($metaClass->getProperties() as $metaProperty) {
            if (!array_key_exists($metaProperty->getName(), $values)) {
                //if(!$metaProperty->getRange()->isClass() || !$metaProperty->getRange()->getCardinality()->isMany()){
                continue;
                //}
                //$values[$metaProperty->getName()] = [];
            }

            $value = $values[$metaProperty->getName()];
            if ($metaProperty->getRange()->isClass()) {
                if (!$metaProperty->getRange()->getCardinality()->isMany()) {
                    //Check if value is scalar, do not hydrate, just pass to parent hydrator, it will be loaded
                    $value = $this->hydrateOne($value, $metaProperty->getRange()->asClass(), $this->resolveObject($metaProperty->getRange()->asClass()->getClassName()));
                } else {
                    //Check if value is array of ids (scalar array), do not hydrate, just pass to parent hydrator, it will be loaded
                    $value = $this->hydrateMany($value, $metaProperty->getRange()->asClass());
                }
            }
            $preparedData[$metaProperty->getName()] = $value;
        }
        return $this->createHydrator($metaClass)->hydrate($preparedData, $this->resolveObject($object));
    }

    private function hydrateMany(array $items, MetaClass $metaClass): array
    {
        $collection = [];
        foreach ($items as $item) {
            $collection[] = $this->hydrateOne($item, $metaClass, $this->resolveObject($metaClass->getClassName()));
        }
        return $collection;
    }

    private function createHydrator(MetaClass $metaClass): DoctrineObject
    {
        return new DoctrineObject($this->objectManager);
    }

    public function hydrateEntity(array $entityArray, string|object $entityOrClass, array $options = []): object
    {
        return $this->hydrateOne($entityArray, $this->metaData->getByClass(is_object($entityOrClass) ? $entityOrClass::class : $entityOrClass), $entityOrClass);
    }

    public function entityCollectionFromJson(iterable $entities, string $className, array $options = []): object
    {
        // TODO: Implement entityCollectionFromJson() method.
    }
}
