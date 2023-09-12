<?php
declare(strict_types=1);

namespace TPG\PMix\Data\Condition;

use TPG\PMix\Data\Condition;
use Stringable;

final class PropertyCondition implements Condition, Stringable
{
    private string $property;
    private Operation $operation;
    private mixed $parameterValue;
    private string $parameterName;


    private function __construct()
    {

    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getOperation(): Operation
    {
        return $this->operation;
    }

    public function getParameterValue(): mixed
    {
        return $this->parameterValue;
    }

    public function getParameterName(): string
    {
        return $this->parameterName;
    }

    public function getParameters(): array
    {
        return [$this->parameterName];
    }

    public function actualize(array $actualParameters): ?Condition
    {
        if ($this->parameterValue) {
            return $this;
        }

        if (array_intersect($this->getParameters(), $actualParameters)) {
            return $this;
        }

        return null;
    }

    public static function createWithValue(string $property, Operation $operation, mixed $value): self
    {
        $condition = new self();
        $condition->property = $property;
        $condition->operation = $operation;
        $condition->parameterValue = $value;
        $condition->parameterName = ConditionUtils::generateParameterName($property);
        return $condition;
    }

    public static function createWithParameter(string $property, Operation $operation, string $parameter): self
    {
        $condition = new self();
        $condition->property = $property;
        $condition->operation = $operation;
        $condition->parameterValue = null;
        $condition->parameterName = $parameter;
        return $condition;
    }

    public static function equal(string $property, mixed $value): self
    {
        return self::createWithValue($property, Operation::EQUAL, $value);
    }

    public function __toString(): string
    {
        return sprintf('[%s %s %s]',
            $this->property,
            $this->operation->value,
            $this->parameterValue ? json_encode($this->parameterValue) : ':' . $this->parameterName
        );
    }


}