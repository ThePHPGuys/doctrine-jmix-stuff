<?php
declare(strict_types=1);

namespace TPG\PMix;

interface QueryParamValueProvider
{
    public function supports(string $parameterName): bool;

    public function getValue(mixed $value): mixed;
}