<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing an email address.
 */
final readonly class EmailAddress implements Stringable
{
    private const MAX_LENGTH = 255;

    public string $address;

    public ?string $name;

    public function __construct(string $address, ?string $name = null)
    {
        $address = trim($address);

        if ($address === '') {
            throw new InvalidValueException('Email address cannot be empty.');
        }

        if (strlen($address) > self::MAX_LENGTH) {
            throw new InvalidValueException(
                sprintf('Email address cannot exceed %d characters.', self::MAX_LENGTH)
            );
        }

        if (filter_var($address, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidValueException(
                sprintf('Invalid email address: %s', $address)
            );
        }

        $this->address = $address;
        $this->name = $name !== null && trim($name) !== '' ? trim($name) : null;
    }

    /**
     * Create from a string (email only) or array with name.
     */
    public static function from(string|self $value, ?string $name = null): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return new self($value, $name);
    }

    /**
     * Get the formatted email address (with name if present).
     */
    public function formatted(): string
    {
        if ($this->name !== null) {
            return sprintf('%s <%s>', $this->name, $this->address);
        }

        return $this->address;
    }

    public function __toString(): string
    {
        return $this->address;
    }

    public function equals(self $other): bool
    {
        return strtolower($this->address) === strtolower($other->address);
    }
}
