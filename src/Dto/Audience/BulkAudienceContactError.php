<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

use Lettr\Enums\BulkAudienceContactErrorCode;

/**
 * A row that was skipped during a bulk create, with its position in the
 * submitted array.
 *
 * The request still succeeds with HTTP 201 when rows are skipped — check
 * {@see BulkStoreAudienceContactsResult::hasErrors()} rather than the status.
 */
final readonly class BulkAudienceContactError
{
    /**
     * @param  int  $index  Zero-based position of the row in the request.
     * @param  BulkAudienceContactErrorCode|string  $errorCode  Kept as a raw string when the API
     *                                                          reports a code this SDK version does not know.
     */
    public function __construct(
        public int $index,
        public ?string $email,
        public BulkAudienceContactErrorCode|string $errorCode,
        public string $error,
    ) {}

    /**
     * @param  array{index: int, email: string|null, error_code: string, error: string}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            index: $data['index'],
            email: $data['email'],
            errorCode: BulkAudienceContactErrorCode::tryFrom($data['error_code']) ?? $data['error_code'],
            error: $data['error'],
        );
    }
}
