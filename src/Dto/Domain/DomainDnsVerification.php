<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DnsStatus;

/**
 * DNS verification result for a specific record type.
 */
final readonly class DomainDnsVerification
{
    public function __construct(
        public DnsStatus $status,
        public ?string $error = null,
        public ?string $expected = null,
        public ?string $found = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     status: string,
     *     error?: string|null,
     *     expected?: string|null,
     *     found?: string|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            status: DnsStatus::from($data['status']),
            error: $data['error'] ?? null,
            expected: $data['expected'] ?? null,
            found: $data['found'] ?? null,
        );
    }

    /**
     * Check if the DNS record is valid.
     */
    public function isValid(): bool
    {
        return $this->status === DnsStatus::Valid;
    }

    /**
     * Check if there's an error.
     */
    public function hasError(): bool
    {
        return $this->error !== null;
    }
}
