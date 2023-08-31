<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

use Misterx\DoctrineJmix\Data\View;
use Misterx\DoctrineJmix\MetaModel\MetaClass;

interface ViewsRepository
{
    public function findMetaClassView(MetaClass $metaClass, string $name): ?View;

    public function getEntityView(string $entityClass, string $name): View;

    public function getMetaClassView(MetaClass $metaClass, string $name): View;
}