<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Datatype;

use Misterx\DoctrineJmix\MetaModel\Datatype;

final readonly class DoctrineDatatype implements Datatype
{
    public function __construct(private string $type)
    {

    }

    public function getType(): string
    {
        return $this->type;
    }
}