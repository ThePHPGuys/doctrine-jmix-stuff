<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Condition;

use Misterx\DoctrineJmix\Data\Condition;

final class ConditionUtils
{
    public static function generateParameterName(string $property): string
    {
        return uniqid(str_replace('.', '_', $property));
    }

    /**
     * @template T of Condition
     * @param Condition $rootCondition
     * @param class-string<T> $nestedConditionClassName
     * @return list<T>
     */
    public static function collectNestedConditions(Condition $rootCondition, string $nestedConditionClassName = Condition::class): array
    {
        $conditions = [];
        if ($rootCondition instanceof LogicalCondition) {
            $childConditions = array_reduce(
                $rootCondition->getConditions(),
                fn(array $stack, Condition $condition) => [
                    ...$stack,
                    ...self::collectNestedConditions($condition, $nestedConditionClassName)],
                []
            );
            $conditions = [...$conditions, ...$childConditions];
        } elseif ($rootCondition instanceof $nestedConditionClassName) {
            $conditions[] = $rootCondition;
        }

        return $conditions;
    }

    public static function isUnaryOperation(PropertyCondition $condition): bool
    {
        return $condition->getOperation() === Operation::IS_SET;
    }

    public static function isCollectionOperation(PropertyCondition $condition): bool
    {
        return in_array($condition->getOperation(), [Operation::IN_LIST, Operation::NOT_IN_LIST]);
    }
}