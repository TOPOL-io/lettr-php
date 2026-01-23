<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing a webhook ID.
 */
final readonly class WebhookId implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('Webhook ID cannot be empty.');
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
