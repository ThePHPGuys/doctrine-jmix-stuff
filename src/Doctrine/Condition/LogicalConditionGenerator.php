<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine\Condition;

use TPG\PMix\Data\Condition;
use TPG\PMix\Data\Condition\LogicalCondition;
use TPG\PMix\Data\Condition\Type;

final readonly class LogicalConditionGenerator implements ConditionGenerator
{
    public function __construct(private ConditionGeneratorResolver $conditionGeneratorResolver)
    {

    }

    public function supports(ConditionGenerationContext $context): bool
    {
        return $context->getCondition() instanceof LogicalCondition;
    }

    public function generateWhere(ConditionGenerationContext $context): string
    {
        $condition = $context->getCondition();
        assert($condition instanceof LogicalCondition);
        if (!$condition->getConditions()) {
            return '';
        }

        $whereParts = [];

        foreach ($condition->getConditions() as $childCondition) {
            $childContext = $context->getChildContexts()[$childCondition];
            $generator = $this->conditionGeneratorResolver->resolve($childContext);
            $whereParts[] = $generator->generateWhere($childContext);
        }
        $whereParts = array_filter($whereParts);

        if (!$whereParts) {
            return '';
        }

        return '( ' . implode($condition->getType() === Type::AND ? ' AND ' : ' OR ', $whereParts) . ' )';
    }

    public function generateJoin(ConditionGenerationContext $context): array
    {
        $condition = $context->getCondition();
        assert($condition instanceof LogicalCondition);

        if (!$condition->getConditions()) {
            return [];
        }
        $joins = [];
        foreach ($condition->getConditions() as $childCondition) {
            $childContext = $context->getChildContexts()[$childCondition];
            $generator = $this->conditionGeneratorResolver->resolve($childContext);
            $childJoins = $generator->generateJoin($childContext);
            $joins = [...$joins, ...$childJoins];
        }
        return $joins;
    }

    public function generateParameterValue(Condition $condition, mixed $parameterValue): mixed
    {
        return null;
    }


}