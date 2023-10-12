<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use Doctrine\Laminas\Hydrator\DoctrineObject;
use Doctrine\Persistence\ObjectManager;
use Laminas\Hydrator\ExtractionInterface;
use TPG\PMix\Data\View;
use TPG\PMix\EntitySerializerInterface;
use TPG\PMix\MetaModel\MetaClass;

final class Serializer implements EntitySerializerInterface
{
    public function __construct(readonly private ObjectManager $objectManager)
    {

    }

    private function createExtractor(): ExtractionInterface
    {
        return new DoctrineObject($this->objectManager);
    }

    public function entityToJson(object $entity, ?View $view = null, array $options = []): string
    {
        return json_encode($this->createExtractor());
    }

    public function entityCollectionToJson(iterable $entities, ?View $view = null, array $options = []): string
    {
        // TODO: Implement entityCollectionToJson() method.
    }

    public function entityFromJson(string $json, MetaClass $metaClass, array $options = []): object
    {
        // TODO: Implement entityFromJson() method.
    }

    public function entityCollectionFromJson(iterable $entities, MetaClass $metaClass, array $options = []): object
    {
        // TODO: Implement entityCollectionFromJson() method.
    }

}
