<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data\LoadContext;

final readonly class ParameterValue
{
    public function __construct(private mixed $value, private mixed $type = null)
    {

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