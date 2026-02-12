<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing a tag.
 */
final readonly class Tag implements Stringable
{
    private const int MAX_LENGTH = 64;

    public string $value;

    /**
     * @throws InvalidValueException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('Tag cannot be empty.');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw new InvalidValueException(
                sprintf('Tag cannot exceed %d characters.', self::MAX_LENGTH)
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
