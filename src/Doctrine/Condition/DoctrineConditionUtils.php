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
            Operation::NOT_EQUAL => '<>',
            Operation::IS_SET => $condition->getParameterValue() ? "IS NOT NULL" : "IS NULL",
            Operation::IN_LIST => 'IN',
            Operation::NOT_IN_LIST => 'NOT IN',
            Operation::CONTAINS,
            Operation::STARTS_WITH,
            Operation::ENDS_WITH => 'LIKE',
            Operation::NOT_CONTAINS => 'NOT LIKE',
            Operation::LESS => '<',
            Operation::LESS_OR_EQUAL => '<=',
            Operation::GREATER => '>',
            Operation::GREATER_OR_EQUAL => '>=',
            default => throw new \InvalidArgumentException('Unsupported operation: ' . $operation->name)
        };
    }
}