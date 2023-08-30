<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

final class Sort
{
    /**
     * @var Order[]
     */
    private array $orders = [];

    /**
     * @param array $orders
     */
    private function __construct(array $orders)
    {
        $this->orders = $orders;
    }

    public function getOrders(): array
    {
        return $this->orders;
    }

    public function isSorted(): bool
    {
        return count($this->orders) > 0;
    }

    public static function by(Order ...$orders): self
    {
        return new self($orders);
    }

    public static function unsorted(): self
    {
        return new self([]);
    }

}