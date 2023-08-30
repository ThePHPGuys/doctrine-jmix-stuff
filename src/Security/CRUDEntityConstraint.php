<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security;

final class CRUDEntityConstraint implements AccessConstraint
{
    public function getContextType(): string
    {
        return CRUDEntityContext::class;
    }

    public function apply(AccessContext $context)
    {
        assert($context instanceof CRUDEntityContext);
    }

}