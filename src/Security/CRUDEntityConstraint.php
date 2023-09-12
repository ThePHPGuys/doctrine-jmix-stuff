<?php
declare(strict_types=1);

namespace TPG\PMix\Security;

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