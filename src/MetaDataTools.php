<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\MetaModel\CascadeType;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\MetaProperty;

class MetaDataTools
{
    public const PRIMARY_KEY_ATTR = '_primary';
    public const SYSTEM_ATTR = '_system';
    public const CASCADE_PROPERTIES_ATTR = '_cascadeProperties';
    public const CASCADE_TYPES_ATTR = '_cascadeProperties';

    public function getPrimaryKeyProperty(MetaClass $metaClass): MetaProperty
    {
        return $metaClass->getProperty($this->getPrimaryKeyPropertyName($metaClass));
    }

    public function getPrimaryKeyPropertyName(MetaClass $metaClass): string
    {
        return $metaClass->getAttributes()->get(self::PRIMARY_KEY_ATTR);
    }

    public function getSystemPropertyNames(MetaClass $metaClass): array
    {
        if (!$metaClass->getAttributes()->has(self::SYSTEM_ATTR)) {
            return [];
        }

        return $metaClass->getAttributes()->get(self::SYSTEM_ATTR);
    }

//    public function getInstanceName(object $instance):string
//    {
//
//    }

    /**
     * @return MetaProperty[]
     */
    public function getInstanceNameRelatedProperties(MetaClass $metaClass): array
    {
        //Temporary return primary key
        return [$this->getPrimaryKeyProperty($metaClass)];
    }

    /**
     * @param MetaProperty $property
     * @return CascadeType[]
     */
    public function getCascadeTypes(MetaProperty $property): array
    {
        if (!$property->getAttributes()->has(self::CASCADE_TYPES_ATTR)) {
            return [];
        }
        $types = $property->getAttributes()->get(self::CASCADE_TYPES_ATTR);
        return array_map(fn(string $type) => CascadeType::from($type), $types);
    }

    /**
     * @param MetaClass $metaClass
     * @param CascadeType $type
     * @return MetaProperty[]
     */
    public function getCascadeProperties(MetaClass $metaClass, CascadeType $type): array
    {
        $classAttributes = $metaClass->getAttributes();
        $cascadePropertyNames = $classAttributes->has(self::CASCADE_PROPERTIES_ATTR) ? $classAttributes->get(self::CASCADE_PROPERTIES_ATTR) : [];
        $cascadeProperties = [];
        foreach ($cascadePropertyNames as $propertyName) {
            $property = $metaClass->getProperty($propertyName);
            if (in_array($type, $this->getCascadeTypes($property))) {
                $cascadeProperties[] = $property;
            }
        }
        return $cascadeProperties;
    }
}
