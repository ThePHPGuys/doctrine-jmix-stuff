<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Condition;

use Misterx\DoctrineJmix\Data\Condition;

interface ConditionGenerator
{
    public function supports(ConditionGenerationContext $context): bool;

    public function generateWhere(ConditionGenerationContext $context): string;

    /**
     * @param ConditionGenerationContext $context
     * @return array<string> - key is joinAlias and value is join property
     */
    public function generateJoin(ConditionGenerationContext $context): array;

    public function generateParameterValue(Condition $condition, mixed $parameterValue): mixed;
}