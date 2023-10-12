<?php
declare(strict_types=1);

namespace TPG\PMix;

use TPG\PMix\Data\View;
use TPG\PMix\MetaModel\MetaClass;

interface EntitySerializerInterface
{
    public function entityToJson(object $entity, ?View $view = null, array $options = []): string;

    public function entityCollectionToJson(iterable $entities, ?View $view = null, array $options = []): string;

    public function entityFromJson(string $json, MetaClass $metaClass, array $options = []): object;

    public function entityCollectionFromJson(iterable $entities, MetaClass $metaClass, array $options = []): object;
}
