<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

interface DataLoader
{
    public function load(mixed $keys):array;
}