<?php

namespace TPG\PMix\MetaModel\Range;

use TPG\PMix\MetaModel\Datatype;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\Range;
use TPG\PMix\MetaModel\RangeCardinality;
use UnitEnum;

final class EnumRange implements Range
{
    public function __construct(private readonly Datatype\Enumeration $enumeration)
    {

    }

    public function isDatatype(): bool
    {
        return false;
    }

    public function isClass(): bool
    {
        return false;
    }

    public function isEnum(): bool
    {
        return true;
    }

    public function asEnum(): Datatype\Enumeration
    {
        return $this->enumeration;
    }

    public function asClass(): MetaClass
    {
        throw new \LogicException('Range is enum');
    }

    public function asDatatype(): Datatype
    {
        throw new \LogicException('Range is enum');
    }

    public function getCardinality(): RangeCardinality
    {
        return RangeCardinality::NONE;
    }

}
