<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when API validation fails.
 */
final class ValidationException extends ApiException
{
    /**
     * @var array<string, array<string>>
     */
    private array $errors;

    /**
     * @param  array<string, array<string>>  $errors
     */
    public function __construct(string $message, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 422, $previous);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors.
     *
     * @return array<string, array<string>>
     */
    public function errors(): array
    {
        return $this->errors;
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
