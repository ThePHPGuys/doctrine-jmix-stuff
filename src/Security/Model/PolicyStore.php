<?php
declare(strict_types=1);

namespace TPG\PMix\Security\Model;

use TPG\PMix\MetaModel\MetaClass;

interface PolicyStore
{
    /**
     * @return ResourcePolicy[]
     */
    public function getEntityPolicies(MetaClass $metaClass): array;

    /**
     * @return ResourcePolicy[]
     */
    public function getEntityPoliciesByWildcard(string $wildcard = '*'): array;

    /**
     * @return ResourcePolicy[]
     */
    public function getEntityAttributePolicies(MetaClass $metaClass, string $attribute): array;

    /**
     * @return ResourcePolicy[]
     */
    public function getEntityAttributePoliciesByWildcard(string $entity, string $attribute): array;
}