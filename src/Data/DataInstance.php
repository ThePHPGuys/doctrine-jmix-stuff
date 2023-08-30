<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

use Misterx\DoctrineJmix\MetaModel\MetaClass;

interface DataInstance
{
    public function getData(): array;

    public function getEntity(): object;

    public function getEntityId(): mixed;

    public function getMetaClass(): MetaClass;
}