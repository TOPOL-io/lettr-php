<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Contracts\Arrayable;

/**
 * Template substitution data (variables).
 */
final readonly class SubstitutionData implements Arrayable
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create from an array.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self($data);
    }

    /**
     * Create empty substitution data.
     */
    public static function empty(): self
    {
        return new self;
    }

    /**
     * Add a key-value pair.
     */
    public function set(string $key, mixed $value): self
    {
        return new self([...$this->data, $key => $value]);
    }

    /**
     * Get a value by key.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a key exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get all data.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Merge with another SubstitutionData instance.
     */
    public function merge(self $other): self
    {
        return new self([...$this->data, ...$other->data]);
    }

    /**
     * Check if substitution data is empty.
     */
    public function isEmpty(): bool
    {
        return count($this->data) === 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
