<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\MetaModel;

interface Datatype
{
    public function getType(): string;
}