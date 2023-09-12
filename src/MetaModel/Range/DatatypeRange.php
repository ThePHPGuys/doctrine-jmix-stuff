<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel\Range;

use TPG\PMix\MetaModel\Datatype;
use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\MetaModel\RangeCardinality;
use TPG\PMix\MetaModel\Range;
use UnitEnum;

final class DatatypeRange implements Range
{
    public function __construct(readonly private Datatype $datatype)
    {

    }

    public function isDatatype(): bool
    {
        return true;
    }

    public function isClass(): bool
    {
        return false;
    }

    public function asClass(): MetaClass
    {
        throw new \LogicException('Range is datatype');
    }

    public function asDatatype(): Datatype
    {
        return $this->datatype;
    }

    public function getCardinality(): RangeCardinality
    {
        return RangeCardinality::NONE;
    }

    public function isEnum(): bool
    {
        return false;
    }

    public function asEnum(): Datatype\Enumeration
    {
        throw new \LogicException('Range is datatype');
    }


}