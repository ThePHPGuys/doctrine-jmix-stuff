<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\Condition;

use Misterx\DoctrineJmix\Data\Condition;

final class LogicalCondition implements Condition, \Stringable
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
        // TODO: Implement actualize() method.
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

    public function __toString()
    {
        return '(' . implode(' ' . $this->type->value . ' ', $this->conditions) . ')';
    }


}