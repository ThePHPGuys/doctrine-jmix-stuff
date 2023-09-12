<?php
declare(strict_types=1);

namespace TPG\PMix\Data;

final readonly class Order
{
    public function __construct(public string $property, public Direction $direction)
    {

    }

    public static function asc(string $property): self
    {
        return new self($property, Direction::ASC);
    }

    public static function desc(string $property): self
    {
        return new self($property, Direction::DESC);
    }
}