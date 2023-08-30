<?php

namespace Misterx\DoctrineJmix\MetaModel;

use Misterx\DoctrineJmix\MetaModel\Datatype\Enumeration;

interface Range
{
    public function isDatatype(): bool;

    public function isClass(): bool;

    public function isEnum(): bool;

    public function asEnum(): Enumeration;

    public function asClass(): MetaClass;

    public function asDatatype(): Datatype;

    public function getCardinality(): RangeCardinality;
}