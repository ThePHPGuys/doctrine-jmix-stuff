<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Declined\Data\FetchPlan;

interface KeyValueExtractor
{
    public function getKey(mixed $row):mixed;
}