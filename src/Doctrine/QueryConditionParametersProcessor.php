<?php
declare(strict_types=1);

namespace TPG\PMix\Doctrine;

use TPG\PMix\Data\Condition;
use TPG\PMix\Doctrine\Condition\ConditionGenerationContext;
use TPG\PMix\Doctrine\Condition\ConditionGeneratorResolver;

final readonly class QueryConditionParametersProcessor
{
    public function __construct(private ConditionGeneratorResolver $conditionGeneratorResolver)
    {
    }

    /**
     * @template T of array<string,mixed>
     * @param T[] $resultParameters
     * @param T[] $queryParameters
     * @return T[]
     */
    public function process(array $resultParameters, array $queryParameters, Condition $actualized): array
    {
        $conditions = Condition\ConditionUtils::collectNestedConditions($actualized, Condition\PropertyCondition::class);
        /** @var Condition\PropertyCondition $condition */
        foreach ($conditions as $condition) {
            $parameterName = $condition->getParameterName();
            if (Condition\ConditionUtils::isUnaryOperation($condition)) {
                unset($resultParameters[$parameterName]);
            }

            if (!isset($queryParameters[$parameterName])) {
                $resultParameters[$parameterName] = $this->generateParameterValue($condition, $condition->getParameterValue());
            } else {
                $resultParameters[$parameterName] = $this->generateParameterValue($condition, $queryParameters[$parameterName]);
            }

        }
        return $resultParameters;
    }

    private function generateParameterValue(Condition $condition, mixed $parameterValue): mixed
    {
        $conditionGenerator = $this->conditionGeneratorResolver->resolve(new ConditionGenerationContext($condition));
        return $conditionGenerator->generateParameterValue($condition, $parameterValue);
    }


}