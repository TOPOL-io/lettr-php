<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing a timestamp.
 */
final readonly class Timestamp implements Stringable
{
    public DateTimeImmutable $value;

    public function __construct(DateTimeImmutable $value)
    {
        $this->value = $value;
    }

    /**
     * Create from an ISO 8601 string.
     */
    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidValueException('Timestamp cannot be empty.');
        }

        $dateTime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value);

        if ($dateTime === false) {
            // Try ISO 8601 with microseconds
            $dateTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.uP', $value);
        }

        if ($dateTime === false) {
            // Try basic ISO format
            $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
        }

        if ($dateTime === false) {
            throw new InvalidValueException(
                sprintf('Invalid timestamp format: %s', $value)
            );
        }

        return new self($dateTime);
    }

    public static function from(string|DateTimeImmutable|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if ($value instanceof DateTimeImmutable) {
            return new self($value);
        }

        return self::fromString($value);
    }

    /**
     * Create a timestamp for the current moment.
     */
    public static function now(): self
    {
        return new self(new DateTimeImmutable);
    }

    /**
     * Get the timestamp as an ISO 8601 string.
     */
    public function toIso8601(): string
    {
        return $this->value->format(DateTimeInterface::ATOM);
    }

    /**
     * Get the timestamp in UTC.
     */
    public function toUtc(): self
    {
        return new self($this->value->setTimezone(new DateTimeZone('UTC')));
    }

    /**
     * Format the timestamp with a custom format.
     */
    public function format(string $format): string
    {
        return $this->value->format($format);
    }

    public function __toString(): string
    {
        return $this->toIso8601();
    }

    public function equals(self $other): bool
    {
        return $this->value->getTimestamp() === $other->value->getTimestamp();
    }

    public function isBefore(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isAfter(self $other): bool
    {
        return $this->value > $other->value;
    }
}
