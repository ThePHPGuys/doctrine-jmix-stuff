<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

final class QueryParamValuesProvider
{
    public function supports(string $paramName): bool
    {
        return false;
    }

    public function getValue(string $paramName): mixed
    {
        return null;
    }
}