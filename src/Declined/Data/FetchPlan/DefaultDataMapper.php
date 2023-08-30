<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

final readonly class DefaultDataMapper implements DataMapper
{
    public function __construct(private string $propertyName, private mixed $defaultValue=null)
    {

    }

    public function map(mixed $referencedKey, array $row, array $referencedData): array
    {
        return [...$row,...[$this->propertyName=>$referencedData[$referencedKey]??$this->defaultValue]];
    }

}