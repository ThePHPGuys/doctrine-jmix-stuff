<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\Data\View;
use TPG\PMix\MetaModel\MetaClass;

interface ViewsRepository
{
    public function findMetaClassView(MetaClass $metaClass, string $name): ?View;

    public function getEntityView(string $entityClass, string $name): View;

    public function getMetaClassView(MetaClass $metaClass, string $name): View;
}