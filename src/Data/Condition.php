<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix\Data;

interface Condition
{
    /**
     * Returns parameter names specified in the condition.
     * @return string[]
     */
    public function getParameters(): array;

    /**
     * Returns the condition if the argument contains all parameters specified in the condition.
     * @param string[] $actualParameters
     */
    public function actualize(array $actualParameters): ?Condition;

}