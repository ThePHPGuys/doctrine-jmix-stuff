<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

interface Datatype
{
    public function getType(): string;
}