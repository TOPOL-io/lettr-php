<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing base64 encoded data.
 */
final readonly class Base64Data implements Stringable
{
    private const PATTERN = '/^[A-Za-z0-9+\/]*={0,2}$/';

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('Base64 data cannot be empty.');
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidValueException('Invalid base64 encoded data.');
        }

        // Verify it's actually valid base64 by attempting to decode
        if (base64_decode($value, true) === false) {
            throw new InvalidValueException('Invalid base64 encoded data.');
        }

        $this->value = $value;
    }

    /**
     * Create from raw binary data.
     */
    public static function fromBinary(string $data): self
    {
        return new self(base64_encode($data));
    }

    public static function from(string|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return new self($value);
    }

    /**
     * Decode the base64 data to raw binary.
     */
    public function decode(): string
    {
        $decoded = base64_decode($this->value, true);

        if ($decoded === false) {
            throw new InvalidValueException('Failed to decode base64 data.');
        }

        return $decoded;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
