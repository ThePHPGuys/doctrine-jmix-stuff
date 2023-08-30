<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\LoadContext;

final class Parameter
{
    private mixed $type;

    public function __construct(private readonly string|int $name, private readonly mixed $value, mixed $type = null)
    {
        $this->type = $type;
    }

    public function getName(): string|int
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function isTypeWasSpecified(): bool
    {
        return $this->type !== null;
    }

}