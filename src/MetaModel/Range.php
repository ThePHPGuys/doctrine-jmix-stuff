<?php

namespace TPG\PMix\MetaModel;

use TPG\PMix\MetaModel\Datatype\Enumeration;

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