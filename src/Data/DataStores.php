<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

final readonly class DataStores
{
    /**
     * @param array<string,DataStore> $stores
     */
    public function __construct(private array $stores)
    {

    }

    public function get(string $storeName): DataStore
    {
        if (!array_key_exists($storeName, $this->stores)) {
            throw new \InvalidArgumentException('Unknown store: ' . $storeName);
        }
        return $this->stores[$storeName];
    }
}