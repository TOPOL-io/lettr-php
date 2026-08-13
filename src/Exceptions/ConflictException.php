<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when a conflict occurs (e.g., resource already exists).
 *
 * @see ContactAlreadyExistsException for the audience-contact specialization.
 */
class ConflictException extends ApiException
{
    public function __construct(
        string $message = 'Resource already exists.',
        ?\Throwable $previous = null,
        ?string $errorCode = null,
    ) {
        parent::__construct($message, 409, $previous, $errorCode);
    }
}
