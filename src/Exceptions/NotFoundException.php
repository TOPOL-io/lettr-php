<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when a resource is not found.
 */
final class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Resource not found.', ?\Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
