<?php
declare(strict_types=1);

namespace TPG\PMix\Security;

final class AccessManager
{
    public function __construct(private ?AccessConstraintsRegistry $registry = null)
    {

    }

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

    public function applyRegisteredConstraints(AccessContext $context): void
    {
        if (!$this->registry) {
            return;
        }
        $this->applyConstraints($context, $this->registry->getConstraints());
    }

    private function log(AccessConstraint $constraint, AccessContext $context): void
    {


    }
}
