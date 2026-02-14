<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DnsStatus;

/**
 * SPF verification details.
 */
final readonly class SpfVerification
{
    public function __construct(
        public bool $isValid,
        public DnsStatus $status,
        public ?string $record,
        public ?string $error,
        public bool $includesSparkpost,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     is_valid: bool,
     *     status: string,
     *     record?: string|null,
     *     error?: string|null,
     *     includes_sparkpost: bool,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            isValid: $data['is_valid'],
            status: DnsStatus::from($data['status']),
            record: $data['record'] ?? null,
            error: $data['error'] ?? null,
            includesSparkpost: $data['includes_sparkpost'],
        );
    }
}
