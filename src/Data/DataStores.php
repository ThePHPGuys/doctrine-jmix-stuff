<?php
declare(strict_types=1);

namespace TPG\PMix\Data;

readonly class DataStores
{
    /**
     * @var array<string,DataStore>
     */
    private array $stores;

    /**
     * @param iterable<string,DataStore> $stores
     */
    public function __construct(iterable $stores)
    {
        $this->stores = $stores instanceof \Traversable ? iterator_to_array($stores) : $stores;
    }

    public function get(string $storeName): DataStore
    {
        if (!array_key_exists($storeName, $this->stores)) {
            throw new \InvalidArgumentException('Unknown store: ' . $storeName);
        }
        return $this->stores[$storeName];
    }
}
