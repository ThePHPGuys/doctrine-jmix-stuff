<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security;

interface AccessConstraint
{
    /**
     * @return class-string
     */
    public function getContextType(): string;

    public function apply(AccessContext $context);
}