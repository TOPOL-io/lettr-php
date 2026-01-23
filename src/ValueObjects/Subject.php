<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing an email subject.
 */
final readonly class Subject implements Stringable
{
    /**
     * RFC 2822 recommends lines should be no more than 998 characters.
     */
    private const MAX_LENGTH = 998;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('Subject cannot be empty.');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidValueException(
                sprintf('Subject cannot exceed %d characters.', self::MAX_LENGTH)
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
