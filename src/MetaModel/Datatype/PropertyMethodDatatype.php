<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel\Datatype;

use TPG\PMix\MetaModel\Datatype;

final readonly class PropertyMethodDatatype implements Datatype
{
    public function __construct(private string $type)
    {

    }

    public function getType(): string
    {
        return $this->type;
    }

}