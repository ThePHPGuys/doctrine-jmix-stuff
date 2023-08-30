<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\MetaProperty;

class MetaDataTools
{
    public const PRIMARY_KEY_ATTR = '_primary';
    public const SYSTEM_FIELD_ATTR = '_system';

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
        if (!$metaClass->getAttributes()->has(self::SYSTEM_FIELD_ATTR)) {
            return [];
        }

        return $metaClass->getAttributes()->get(self::SYSTEM_FIELD_ATTR);
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
}