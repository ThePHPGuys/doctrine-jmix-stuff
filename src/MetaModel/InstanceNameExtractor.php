<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel;

final class InstanceNameExtractor
{
    private array $cache = [];

    public function getRef(MetaClass $metaClass): ?InstanceNameRef
    {
        if (array_key_exists($metaClass->getName(), $this->cache)) {
            return $this->cache[$metaClass->getName()];
        }
        $this->extractInstanceNameFromAnnotation($metaClass);
    }


}