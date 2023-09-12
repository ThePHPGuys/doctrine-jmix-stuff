<?php

namespace TPG\PMix\Security;

use TPG\PMix\MetaModel\MetaClass;
use TPG\PMix\Security\Model\PolicyStore;

interface SecurityOperations
{
    public function isEntityCreateAllowed(MetaClass $metaClass, PolicyStore $policies): bool;

    public function isEntityReadAllowed(MetaClass $metaClass, PolicyStore $policies): bool;

    public function isEntityUpdateAllowed(MetaClass $metaClass, PolicyStore $policies): bool;

    public function isEntityDeleteAllowed(MetaClass $metaClass, PolicyStore $policies): bool;

    public function isEntityAttrReadAllowed(string $propertyPath, MetaClass $metaClass, PolicyStore $policies): bool;

    public function isEntityAttrUpdateAllowed(string $propertyPath, MetaClass $metaClass, PolicyStore $policies): bool;
}