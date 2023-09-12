<?php
declare(strict_types=1);

namespace TPG\PMix\MetaModel;

final class MetaAttributes
{
    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(private array $attributes = [])
    {

    }

    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Unknown key: ' . $key);
        }
        return $this->attributes[$key];
    }

    public function set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }
}