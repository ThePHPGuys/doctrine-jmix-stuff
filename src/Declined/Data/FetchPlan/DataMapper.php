<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

interface DataMapper
{
    public function map(mixed $referencedKey, array $row, array $referencedData):array;
}