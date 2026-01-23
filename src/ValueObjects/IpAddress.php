<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing an IP address.
 */
final readonly class IpAddress implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('IP address cannot be empty.');
        }

        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
            throw new InvalidValueException(
                sprintf('Invalid IP address: %s', $value)
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

    public function isIPv4(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public function isIPv6(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
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
