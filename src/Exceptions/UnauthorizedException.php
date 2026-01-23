<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when authentication fails.
 */
final class UnauthorizedException extends ApiException
{
    public function __construct(string $message = 'Invalid API key.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
