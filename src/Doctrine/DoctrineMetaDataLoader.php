<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Mapping\MappingException;
use Misterx\DoctrineJmix\Doctrine\Datatype\DoctrineDatatype;
use Misterx\DoctrineJmix\MetaDataTools;
use Misterx\DoctrineJmix\MetaModel\Attribute\Attribute;
use Misterx\DoctrineJmix\MetaModel\Attribute\Composition;
use Misterx\DoctrineJmix\MetaModel\Attribute\Property;
use Misterx\DoctrineJmix\MetaModel\Datatype;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaData;
use Misterx\DoctrineJmix\MetaModel\MetaProperty;
use Misterx\DoctrineJmix\MetaModel\MetaPropertyType;
use Misterx\DoctrineJmix\MetaModel\Range\ClassRange;
use Misterx\DoctrineJmix\MetaModel\Range\DatatypeRange;
use Misterx\DoctrineJmix\MetaModel\Range\EnumRange;
use Misterx\DoctrineJmix\MetaModel\RangeCardinality;

final class DoctrineMetaDataLoader
{
    public function __construct(private ClassMetadataFactory $doctrineMetadataFactory)
    {

    }

    /**
     * @param class-string[] $classes
     * @throws MappingException
     * @throws \ReflectionException
     */
    public function load(array $classes, MetaData $metaData, string $defaultStore = 'doctrine'): void
    {
        foreach ($classes as $class) {
            //Store can be parsed by annotation
            $this->createMetaClass($metaData, $class)->setStore($defaultStore);
        }

        foreach ($classes as $class) {
            $this->loadClass($metaData, $class, $this->doctrineMetadataFactory);
        }
        $this->fillPropertyInverse($metaData, $this->doctrineMetadataFactory);
    }

    private function fillPropertyInverse(MetaData $metaData, ClassMetadataFactory $classMetadataFactory): void
    {
        foreach ($metaData->getClasses() as $class) {
            foreach ($class->getProperties() as $property) {
                if (!$property->getRange()->isClass() || !$property->getRange()->getCardinality()->isMany()) {
                    continue;
                }
                $this->assignInverse($classMetadataFactory, $property);
            }
        }
    }

    private function assignInverse(ClassMetadataFactory $metadataFactory, MetaProperty $property): void
    {
        $doctrineMetadata = $metadataFactory->getMetadataFor($property->getMetaClass()->getClassName());
        if (!$doctrineMetadata->isAssociationInverseSide($property->getName())) {
            return;
        }
        $inversePropertyName = $doctrineMetadata->getAssociationMappedByTargetField($property->getName());
        $inverseProperty = $property->getRange()->asClass()->getProperty($inversePropertyName);
        $property->setInverse($inverseProperty);
        $inverseProperty->setInverse($property);
    }


//
//    private function resolveTargetPropertyName(ClassMetadataFactory $classMetadataFactory, string $sourceClass, string $associationName):string
//    {
//        $sourceMetadata = $classMetadataFactory->getMetadataFor($sourceClass);
//        $targetMetadata = $classMetadataFactory->getMetadataFor($sourceMetadata->getAssociationTargetClass($associationName));
//        assert($sourceMetadata instanceof ClassMetadataInfo);
//        assert($targetMetadata instanceof ClassMetadataInfo);
//
//        $sourceAssociation = $sourceMetadata->getAssociationMapping($associationName);
//
//        print_r($sourceAssociation);
//
//        switch ($sourceAssociation['type']){
//            case ClassMetadataInfo::ONE_TO_ONE:
//            case ClassMetadataInfo::MANY_TO_ONE:
//            case ClassMetadataInfo::ONE_TO_MANY:
//                $targetFields = array_values($sourceAssociation['sourceToTargetKeyColumns']);
//                break;
//            case ClassMetadataInfo::MANY_TO_MANY:
//                $targetFields = array_values($sourceAssociation['relationToTargetKeyColumns']);
//                break;
//            default: throw new \LogicException('Unknown relation');
//        }
//
//        if(count($targetFields)>1){
//            throw new \LogicException('Multiple fields association are not supported');
//        }
//
//        return $targetFields[0];
//    }

    private function createMetaClass(MetaData $metaData, string $class): MetaClass
    {
        $existingMetaClass = $metaData->findByClass($class);

        if ($existingMetaClass !== null) {
            return $existingMetaClass;
        }

        $metaClass = new MetaClass($this->getMetaClassName($class), $class);
        $metaData->register($metaClass);
        return $metaClass;
    }

    private function getMetaClassName(string $class): string
    {
        return strtolower(str_ireplace('\\', '_', $class));
    }

    /**
     * @param MetaData $metaData
     * @param class-string $class
     */
    private function loadClass(MetaData $metaData, string $class, ClassMetadataFactory $doctrineMetadata): void
    {
        if ($metaData->findByClass($class) === null) {
            throw new \Exception('Unknown class');
        }
        $doctrineClassMetadata = $doctrineMetadata->getMetadataFor($class);
        assert($doctrineClassMetadata instanceof ClassMetadata);

        $this->initProperties($metaData, $class, $doctrineClassMetadata);
    }

    private function initProperties(MetaData $metaData, string $class, ClassMetadata $doctrineClassMetadata): void
    {
        $metaClass = $metaData->getByClass($class);
        foreach ($doctrineClassMetadata->getFieldNames() as $fieldName) {
            if ($metaClass->hasProperty($fieldName)) {
                //Skip already defined fields
                continue;
            }
            $property = $this->initPropertyFromField($metaClass, $fieldName, $doctrineClassMetadata->getFieldMapping($fieldName));
            $this->onDoctrinePropertyCreated($property, $doctrineClassMetadata);
            $this->onPropertyCreated($property, $doctrineClassMetadata->reflFields[$fieldName]);
            if ($doctrineClassMetadata->isReadOnly) {
                $property->setReadonly(true);
            }
        }

        foreach ($doctrineClassMetadata->getAssociationNames() as $associationName) {
            if ($metaClass->hasProperty($associationName)) {
                //Skip already defined fields
                continue;
            }
            $property = $this->initPropertyFromAssociation($metaData, $metaClass, $associationName, $doctrineClassMetadata->getAssociationMapping($associationName));
            $this->onDoctrinePropertyCreated($property, $doctrineClassMetadata);
            $this->onPropertyCreated($property, $doctrineClassMetadata->reflFields[$associationName]);
            if ($doctrineClassMetadata->isReadOnly) {
                $property->setReadonly(true);
            }
        }

        //Add virtual properties
        $reflClass = new \ReflectionClass($class);
        foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $property = $this->initPropertyFromMethod($metaClass, $method);
            if (!$property) {
                continue;
            }
            $this->onPropertyCreated($property, $method);
        }
    }

    private function initPropertyFromMethod(MetaClass $metaClass, \ReflectionMethod $method): ?MetaProperty
    {
        $methodName = $method->getName();
        $returnType = $method->getReturnType();

        if (!str_starts_with($methodName, 'get')) {
            return null;
        }
        //Allow only one type for column definition
        if (!($returnType instanceof \ReflectionNamedType)) {
            return null;
        }
        //Skip void method
        if ($returnType->getName() === 'void') {
            return null;
        }

        if (!$this->isMetaPropertyMethod($method)) {
            return null;
        }
        $propertyName = lcfirst(substr($methodName, 3));
        if ($metaClass->hasProperty($propertyName)) {
            return null;
        }
        $property = new MetaProperty($metaClass, $propertyName);
        $property->setRange(new DatatypeRange(new Datatype\PropertyMethodDatatype($returnType->getName())));
        $property->setType(MetaPropertyType::DATATYPE);
        $property->setTransient(true);
        return $property;
    }

    private function isMetaPropertyMethod(\ReflectionMethod $method): bool
    {
        return count($method->getAttributes(Property::class)) > 0;
    }

    private function initPropertyFromField(MetaClass $metaClass, string $fieldName, array $fieldMapping): MetaProperty
    {
        $property = new MetaProperty($metaClass, $fieldName);
        $property->setRequired(!$fieldMapping['nullable']);
        if (isset($fieldMapping['enumType'])) {
            $property->setRange(new EnumRange(new Datatype\Enumeration($fieldMapping['enumType'])));
            $property->setType(MetaPropertyType::ENUM);
        } else {
            $property->setRange(new DatatypeRange(new DoctrineDatatype($fieldMapping['type'])));
            $property->setType(MetaPropertyType::DATATYPE);
        }
        return $property;
    }

    private function initPropertyFromAssociation(MetaData $metaData, MetaClass $metaClass, string $associationName, array $associationMapping): MetaProperty
    {
        $targetMetaClass = $metaData->findByClass($associationMapping['targetEntity']);
        if ($targetMetaClass === null) {
            throw new \LogicException($associationMapping['targetEntity'] . ' out of metadata scope');
        }
        $property = new MetaProperty($metaClass, $associationName);
        $property->setRange(new ClassRange($targetMetaClass, $this->getCardinality($associationMapping['type'])));
        $property->setType(MetaPropertyType::COMPOSITION);
        return $property;
    }

    private function onDoctrinePropertyCreated(MetaProperty $property, ClassMetadata $classMetadata): void
    {
        if ($classMetadata->isIdentifier($property->getName())) {
            $property->getAttributes()->set(MetaDataTools::PRIMARY_KEY_ATTR, true);
            $property->getMetaClass()->getAttributes()->set(MetaDataTools::PRIMARY_KEY_ATTR, $property->getName());
        }
    }

    private function onPropertyCreated(MetaProperty $property, \ReflectionMethod|\ReflectionProperty $reflection): void
    {
        $this->loadPropertyAttributes($property, $reflection);
        $this->resolveReadonly($property, $reflection);

        $classAttributes = $property->getMetaClass()->getAttributes();
        if ($this->isSystem($property)) {
            $property->getAttributes()->set(MetaDataTools::SYSTEM_FIELD_ATTR, true);
            if (!$classAttributes->has(MetaDataTools::SYSTEM_FIELD_ATTR)) {
                $classAttributes->set(MetaDataTools::SYSTEM_FIELD_ATTR, [$property->getName()]);
            } else {
                $prevSystem = $classAttributes->get(MetaDataTools::SYSTEM_FIELD_ATTR);
                $classAttributes->set(MetaDataTools::SYSTEM_FIELD_ATTR, [...$prevSystem, $property->getName()]);
            }
        }
    }

    private function resolveReadonly(MetaProperty $property, \ReflectionMethod|\ReflectionProperty $reflection)
    {
        if ($reflection instanceof \ReflectionProperty) {
            if ($reflection->isReadOnly()) {
                $property->setReadonly(true);
                return;
            }
            if ($reflection->isPublic()) {
                $property->setReadonly(false);
                return;
            }
            //Try to find method
        }
    }

    private function isSystem(MetaProperty $property): bool
    {
        //System fields are : Id, CreatedDate,CreatedBy,LastModifiedDate,LastModifiedBy, etc.
        // System fields will always be returned
        // For now only id
        return $property->getAttributes()->has(MetaDataTools::PRIMARY_KEY_ATTR);
    }

    private function loadPropertyAttributes(MetaProperty $property, \ReflectionMethod|\ReflectionProperty $reflection): void
    {
        $reflectionAttributes = $reflection->getAttributes(Attribute::class, \ReflectionAttribute::IS_INSTANCEOF);
        /**
         * if($range->isClass()){
         * if(count($reflection->getAttributes(Composition::class))){
         * $property->setType(MetaPropertyType::COMPOSITION);
         * }else{
         * $property->setType(MetaPropertyType::ASSOCIATION);
         * }
         * }
         */
    }

    private function getCardinality(int $doctrineType): RangeCardinality
    {
        return match ($doctrineType) {
            ClassMetadataInfo::ONE_TO_ONE => RangeCardinality::ONE_TO_ONE,
            ClassMetadataInfo::ONE_TO_MANY => RangeCardinality::ONE_TO_MANY,
            ClassMetadataInfo::MANY_TO_ONE => RangeCardinality::MANY_TO_ONE,
            ClassMetadataInfo::MANY_TO_MANY => RangeCardinality::MANY_TO_MANY,
            default => throw new \InvalidArgumentException('Unknown cardinality')
        };
    }
}