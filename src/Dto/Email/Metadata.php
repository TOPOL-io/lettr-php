<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Contracts\Arrayable;

/**
 * Email metadata (key-value pairs).
 */
final readonly class Metadata implements Arrayable
{
    /**
     * @var array<string, string>
     */
    private array $data;

    /**
     * @param  array<string, string>  $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create from an array.
     *
     * @param  array<string, string>  $data
     */
    public static function from(array $data): self
    {
        return new self($data);
    }

    /**
     * Create empty metadata.
     */
    public static function empty(): self
    {
        return new self;
    }

    /**
     * Add a key-value pair.
     */
    public function set(string $key, string $value): self
    {
        return new self([...$this->data, $key => $value]);
    }

    /**
     * Get a value by key.
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if a key exists.
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get all data.
     *
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Check if metadata is empty.
     */
    public function isEmpty(): bool
    {
        return count($this->data) === 0;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
