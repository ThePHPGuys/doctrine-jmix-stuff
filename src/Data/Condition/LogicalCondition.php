<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Condition;

use Misterx\DoctrineJmix\Data\Condition;

final class LogicalCondition implements Condition
{
    /**
     * @var Condition[]
     */
    private array $conditions;

    public function __construct(private readonly Type $type, Condition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function add(Condition $condition)
    {
        $this->conditions[] = $condition;
    }

    /**
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getParameters(): array
    {
        $parameters = [];
        foreach ($this->conditions as $condition) {
            $parameters = [...$parameters, ...$condition->getParameters()];
        }
        return $parameters;
    }

    public function actualize(array $actualParameters): ?Condition
    {
        $copy = new self($this->type);
        foreach ($this->conditions as $condition) {
            $actualized = $condition->actualize($actualParameters);
            if (!$actualized) {
                continue;
            }
            $copy->add($actualized);
        }
        if (!$copy->conditions) {
            return null;
        }
        if (count($copy->conditions) === 1) {
            return $copy->conditions[0];
        }

        return $copy;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public static function and(Condition ...$conditions): self
    {
        return new self(Type::AND, ...$conditions);
    }

    public static function or(Condition ...$conditions): self
    {
        return new self(Type::OR, ...$conditions);
    }

    public function __toString(): string
    {
        return '(' . implode(' ' . $this->type->value . ' ', $this->conditions) . ')';
    }


}