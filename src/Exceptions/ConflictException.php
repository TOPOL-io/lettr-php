<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when a conflict occurs (e.g., resource already exists).
 */
final class ConflictException extends ApiException
{
    public function __construct(string $message = 'Resource already exists.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }
}
