<?php

namespace Misterx\DoctrineJmix\MetaModel\Range;

use Misterx\DoctrineJmix\MetaModel\Datatype;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\Range;
use Misterx\DoctrineJmix\MetaModel\RangeCardinality;
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
