<?php
declare(strict_types=1);

namespace TPG\PMix\Data;

use TPG\PMix\MetaModel\MetaClass;

interface DataInstance
{
    public function getData(): array;

    public function getEntity(): object;

    public function getEntityId(): mixed;

    public function getMetaClass(): MetaClass;
}