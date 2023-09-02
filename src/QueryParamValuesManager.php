<?php
declare(strict_types=1);

namespace Misterx\DoctrineJmix;

final readonly class QueryParamValuesManager
{
    /**
     * @param iterable<QueryParamValueProvider> $providers
     */
    public function __construct(private iterable $providers = [])
    {

    }

    public function supports(string $parameterName): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($parameterName)) {
                return true;
            }
        }
        return false;
    }

    public function getValue(string $parameterName): mixed
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($parameterName)) {
                return $provider->getValue($parameterName);
            }
        }
        return null;
    }
}