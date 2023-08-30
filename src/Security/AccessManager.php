<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security;

final class AccessManager
{
    /**
     * @param AccessConstraint[] $constraints
     */
    public function applyConstraints(AccessContext $context, array $constraints): void
    {
        foreach ($constraints as $constraint) {
            if ($constraint->getContextType() !== get_class($context)) {
                continue;
            }
            $constraint->apply($context);
            $this->log($constraint, $context);
        }
    }

    private function log(AccessConstraint $constraint, AccessContext $context): void
    {


    }
}