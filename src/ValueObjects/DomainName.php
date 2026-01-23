<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing a domain name.
 */
final readonly class DomainName implements Stringable
{
    private const PATTERN = '/^(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/';

    public string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            throw new InvalidValueException('Domain name cannot be empty.');
        }

        if (strlen($value) > 253) {
            throw new InvalidValueException('Domain name cannot exceed 253 characters.');
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidValueException(
                sprintf('Invalid domain name: %s', $value)
            );
        }

        $this->value = $value;
    }

    public static function from(string|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return new self($value);
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
