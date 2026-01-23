<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when a value object receives an invalid value.
 */
final class InvalidValueException extends LettrException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
