<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Security\Model;

interface ResourcePolicy
{
    /**
     * Returns policy type. Standard policies type are: entity, entityAttribute
     * They are listed in @see PolicyType
     */
    public function getType(): string;

    /**
     * Returns a resource, for entity - entity name (MetaClass::getName()),
     * for entityAttribute entityName dot attribute
     */
    public function getResource(): string;

    /**
     * The action is an operation that policy allows or denies
     */
    public function getAction(): string;

    /**
     * Policy effect, usually it is "allow" or "deny"
     * @see PolicyEffect
     */
    public function getEffect(): string;
}