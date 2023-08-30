<?php

namespace Misterx\DoctrineJmix\MetaModel;

abstract class MetaObject
{
    private string $name;
    private MetaAttributes $attributes;

    public function __construct($name)
    {
        $this->attributes = new MetaAttributes();
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): MetaAttributes
    {
        return $this->attributes;
    }
}