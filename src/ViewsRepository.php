<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;

interface ViewsRepository
{
    public function findMetaClassView(MetaClass $metaClass, string $name): ?View;
}