<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DnsStatus;

/**
 * DMARC verification details.
 */
final readonly class DmarcVerification
{
    public function __construct(
        public bool $isValid,
        public DnsStatus $status,
        public ?string $foundAtDomain,
        public ?string $record,
        public ?string $policy,
        public ?string $subdomainPolicy,
        public ?string $error,
        public bool $coveredByParentPolicy,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     is_valid: bool,
     *     status: string,
     *     found_at_domain?: string|null,
     *     record?: string|null,
     *     policy?: string|null,
     *     subdomain_policy?: string|null,
     *     error?: string|null,
     *     covered_by_parent_policy: bool,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            isValid: $data['is_valid'],
            status: DnsStatus::from($data['status']),
            foundAtDomain: $data['found_at_domain'] ?? null,
            record: $data['record'] ?? null,
            policy: $data['policy'] ?? null,
            subdomainPolicy: $data['subdomain_policy'] ?? null,
            error: $data['error'] ?? null,
            coveredByParentPolicy: $data['covered_by_parent_policy'],
        );
    }
}
