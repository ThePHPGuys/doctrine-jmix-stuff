<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Doctrine\Condition;

use Misterx\DoctrineJmix\Data\Condition\Operation;
use Misterx\DoctrineJmix\Data\Condition\PropertyCondition;

final class DoctrineConditionUtils
{
    public static function getDQlOperation(PropertyCondition $condition): string
    {
        $operation = $condition->getOperation();
        return match ($operation) {
            Operation::EQUAL => '=',
            Operation::IS_SET => $condition->getValue() ? "IS NOT NULL" : "IS NULL",
            Operation::IN_LIST => 'IN',
            Operation::NOT_IN_LIST => 'NOT IN',
            default => throw new \InvalidArgumentException('Unsupported operation: ' . $operation->name)
        };
    }
}