<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

/**
 * Exception thrown when creating an audience contact whose email is already in
 * the team's audience (HTTP 409, `resource_already_exists`).
 *
 * This is a client-correctable condition, not an outage — **do not retry it**.
 * Update the existing contact with
 * {@see \Lettr\Services\Audience\AudienceContactService::update()}, or use
 * {@see \Lettr\Services\Audience\AudienceContactService::bulkCreate()} with
 * `updateExisting: true`.
 *
 * Older API versions surfaced this as an HTTP 500 with the misleading
 * `send_error` code, which arrived as a plain {@see ApiException}. Extending
 * {@see ConflictException} keeps existing `catch (ConflictException)` and
 * `catch (ApiException)` handlers working unchanged.
 */
final class ContactAlreadyExistsException extends ConflictException
{
    /**
     * @param  string|null  $email  The address that collided, when the SDK knows it.
     */
    public function __construct(
        string $message = 'A contact with this email already exists.',
        public readonly ?string $email = null,
        ?\Throwable $previous = null,
        ?string $errorCode = null,
    ) {
        parent::__construct($message, $previous, $errorCode);
    }

    /**
     * Re-wrap a generic 409 raised by the transporter, preserving the API
     * message, error code and the original exception chain.
     */
    public static function fromConflict(ConflictException $conflict, ?string $email = null): self
    {
        return new self(
            message: $conflict->getMessage(),
            email: $email,
            previous: $conflict->getPrevious() ?? $conflict,
            errorCode: $conflict->errorCode,
        );
    }
}
