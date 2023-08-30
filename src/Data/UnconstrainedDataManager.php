<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

interface UnconstrainedDataManager
{
    public function load(LoadContext $context): ?object;

    public function loadList(LoadContext $context): iterable;

    public function getCount(LoadContext $context): int;

    public function save(SaveContext $context);

    public function remove(SaveContext $context);
}