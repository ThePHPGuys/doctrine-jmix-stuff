<?php
declare(strict_types=1);

namespace TPG\PMix\Data;

interface UnconstrainedDataManager
{
    public function load(LoadContext $context): object|array|null;

    public function loadList(LoadContext $context): iterable;

    public function getCount(LoadContext $context): int;

    public function save(SaveContext $context);

    public function removeEntity(object ...$entities);

    public function saveEntity(object ...$entities);
}