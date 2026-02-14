<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when API validation fails.
 */
final class ValidationException extends ApiException
{
    /**
     * @param  array<string, array<string>>  $errors
     */
    public function __construct(
        string $message,
        /** @var array<string, array<string>> */
        public readonly array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 422, $previous);
    }

    /**
     * Get errors for a specific field.
     *
     * @return array<string>
     */
    public function errorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * Check if a field has errors.
     */
    public function hasErrorFor(string $field): bool
    {
        return isset($this->errors[$field]) && count($this->errors[$field]) > 0;
    }
}
