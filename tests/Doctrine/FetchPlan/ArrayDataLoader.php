<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Tests\Doctrine\FetchPlan;

use Misterx\DoctrineJmix\Declined\Data\FetchPlan\DataLoader;

final class ArrayDataLoader implements DataLoader
{
    public function __construct(private array $data, private string $column, private bool $asCollections = false)
    {

    }

    public function load(mixed $keys): array
    {
        $loaded = [];
        foreach ($this->data as $row) {
            if (!array_key_exists($this->column, $row)) {
                continue;
            }
            $currentKey = $row[$this->column];
            if (!in_array($currentKey, $keys)) {
                continue;
            }
            if ($this->asCollections) {
                $loaded[$currentKey][] = $row;
            } else {
                $loaded[$currentKey] = $row;
            }
        }
        return $loaded;
    }

}