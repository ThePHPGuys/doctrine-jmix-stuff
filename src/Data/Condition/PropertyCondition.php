<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Condition;

use Misterx\DoctrineJmix\Data\Condition;
use Stringable;

final readonly class PropertyCondition implements Condition, Stringable
{
    private string $property;
    private Operation $operation;
    private mixed $value;
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

    public function getValue(): mixed
    {
        return $this->value;
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
        if ($this->value) {
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
        $condition->value = $value;
        $condition->parameterName = ConditionUtils::generateParameterName($property);
        return $condition;
    }

    public static function createWithParameter(string $property, Operation $operation, string $parameter): self
    {
        $condition = new self();
        $condition->property = $property;
        $condition->operation = $operation;
        $condition->value = null;
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
            $this->value ? json_encode($this->value) : ':' . $this->parameterName
        );
    }


}