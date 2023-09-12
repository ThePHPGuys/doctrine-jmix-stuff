<?php

namespace TPG\PMix\MetaModel\Datatype;

use TPG\PMix\MetaModel\Datatype;

final class Enumeration implements Datatype
{
    /**
     * @param class-string $type
     */
    public function __construct(private readonly string $type)
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValues(): array
    {
        return [$this->type, 'cases']();
    }

}
