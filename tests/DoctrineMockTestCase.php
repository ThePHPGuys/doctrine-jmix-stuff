<?php
declare(strict_types=1);

namespace TPG\PMix\Tests;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;
use PHPUnit\Framework\TestCase;
use TPG\PMix\Doctrine\DoctrineMetaDataLoader;
use TPG\PMix\MetaModel\MetaData;
use TPG\PMix\Tests\Assets\ToManyEntity;
use TPG\PMix\Tests\Assets\ToOneEntity;
use TPG\PMix\Tests\Assets\NoRelationEntity;

class DoctrineMockTestCase extends TestCase
{
    protected ClassMetadataFactory&MockObject $classMetadataFactory;
    protected ObjectManager&MockObject $objectManager;

    protected array $configuredClasses = [];
    protected array $existingObjects = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->classMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $this->classMetadataFactory->method('getMetadataFor')->willReturnCallback(fn($class) => $this->configuredClasses[$class]);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->objectManager->method('getClassMetadata')->willReturnCallback(fn($class) => $this->classMetadataFactory->getMetadataFor($class) ?? throw new \Exception('Not defined entity'));
        $this->objectManager->method('find')->willReturnCallback(fn($class, $id) => $this->findExistingObject($class, $id));
        $this->configuredClasses = [];
        $this->existingObjects = [];
    }

    private function findExistingObject(string $class, $id)
    {
        $filtered = array_filter($this->existingObjects, fn(object $object) => $object::class === $class);
        foreach ($filtered as $item) {
            if (!method_exists($item, 'getId')) {
                continue;
            }
            if ($item->getId() !== $id['id']) {
                continue;
            }
            return $item;
        }
    }

    protected function addExistingObject($object)
    {
        $this->existingObjects[] = $object;
    }

    private function fillReflFields(string $class, ClassMetadata&MockObject $classMetadata): void
    {
        $refl = new \ReflectionClass($class);
        $fields = [...$classMetadata->getFieldNames() ?? [], ...$classMetadata->getAssociationNames() ?? []];
        foreach ($fields as $fieldName) {
            $classMetadata->reflFields[$fieldName] = $refl->getProperty($fieldName);
        }
    }

    protected function configureNoRelationEntity($class = NoRelationEntity::class)
    {
        $metaClassMock = $this->createConfiguredMock(ClassMetadata::class,
            [
                'getName' => $class,
                'getIdentifierFieldNames' => ['id'],
                'getFieldNames' => ['id', 'field'],
                'getAssociationNames' => [],
                'getFieldMapping' => new ReturnCallback(fn($field) => match ($field) {
                    'id', 'field' => [
                        'nullable' => false,
                        'enumType' => null,
                        'type' => 'string'
                    ],
                }),
                'isIdentifier' => new ReturnCallback(fn($field) => $field === 'id')
            ]
        );
        $this->fillReflFields($class, $metaClassMock);
        $this->configuredClasses[$class] = $metaClassMock;
    }

    protected function configureToOne($class = ToOneEntity::class)
    {
        $this->configureNoRelationEntity();
        $metaClassMock = $this->createConfiguredMock(ClassMetadata::class,
            [
                'getName' => $class,
                'getFieldNames' => ['id', 'field'],
                'getAssociationNames' => ['toOne'],
                'getIdentifierFieldNames' => ['id'],
                'getAssociationMapping' => new ReturnCallback(fn($field) => match ($field) {
                    'toOne' => [
                        'targetEntity' => NoRelationEntity::class,
                        'type' => ClassMetadataInfo::ONE_TO_ONE
                    ],
                }),
                'getFieldMapping' => new ReturnCallback(fn($field) => match ($field) {
                    'id', 'field' => [
                        'nullable' => false,
                        'enumType' => null,
                        'type' => 'string'
                    ],
                }),
                'isIdentifier' => new ReturnCallback(fn($field) => $field === 'id'),
            ]
        );
        $this->fillReflFields($class, $metaClassMock);
        $this->configuredClasses[$class] = $metaClassMock;
    }

    protected function configureToMany($class = ToManyEntity::class)
    {
        $this->configureNoRelationEntity();
        $metaClassMock = $this->createConfiguredMock(ClassMetadata::class,
            [
                'getName' => $class,
                'getIdentifierFieldNames' => ['id'],
                'getFieldNames' => ['id', 'field'],
                'getAssociationNames' => ['entities'],
                'getAssociationMapping' => new ReturnCallback(fn($field) => match ($field) {
                    'entities' => [
                        'targetEntity' => NoRelationEntity::class,
                        'type' => ClassMetadataInfo::ONE_TO_MANY
                    ],
                }),
                'getFieldMapping' => new ReturnCallback(fn($field) => match ($field) {
                    'id', 'field' => [
                        'nullable' => false,
                        'enumType' => null,
                        'type' => 'string'
                    ],
                }),
                'hasAssociation' => new ReturnCallback(fn($field) => $field === 'entities'),
                'isSingleValuedAssociation' => false,
                'isCollectionValuedAssociation' => new ReturnCallback(fn($field) => $field === 'entities'),
                'getAssociationTargetClass' => new ReturnCallback(fn($field) => $field === 'entities' ? NoRelationEntity::class : null),
                'isIdentifier' => new ReturnCallback(fn($field) => $field === 'id'),
            ]
        );
        $this->fillReflFields($class, $metaClassMock);
        $this->configuredClasses[$class] = $metaClassMock;
    }

    protected function loadMetaData(): MetaData
    {
        $metadata = new MetaData();
        $loader = new DoctrineMetaDataLoader($this->classMetadataFactory);
        $loader->load(array_keys($this->configuredClasses), $metadata);
        return $metadata;
    }

}

