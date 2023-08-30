<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

final class ArrayKeyExtractor implements KeyValueExtractor
{
    public function __construct(private readonly string $propertyName, private readonly mixed $defaultValue = null)
    {

    }

    public function getKey(mixed $row): mixed
    {
        assert(is_array($row));
        return $row[$this->propertyName]?:$this->defaultValue;
    }

}