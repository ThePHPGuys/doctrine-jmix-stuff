<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel\Range;

use Misterx\DoctrineJmix\MetaModel\Datatype;
use Misterx\DoctrineJmix\MetaModel\MetaClass;
use Misterx\DoctrineJmix\MetaModel\Range;
use Misterx\DoctrineJmix\MetaModel\RangeCardinality;
use UnitEnum;

final class ClassRange implements Range
{

    public function __construct(readonly private MetaClass $class, private readonly RangeCardinality $cardinality)
    {
        assert($cardinality != RangeCardinality::NONE);
    }

    public function isDatatype(): bool
    {
        return false;
    }

    public function isClass(): bool
    {
        return true;
    }

    public function asClass(): MetaClass
    {
        return $this->class;
    }

    public function asDatatype(): Datatype
    {
        throw new \LogicException('Range is class');
    }

    public function getCardinality(): RangeCardinality
    {
        return $this->cardinality;
    }

    public function isEnum(): bool
    {
        return false;
    }

    public function asEnum(): Datatype\Enumeration
    {
        throw new \LogicException('Range is class');
    }

}