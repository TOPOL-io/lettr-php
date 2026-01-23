<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DnsStatus;
use Lettr\Enums\DomainStatus;
use Lettr\ValueObjects\DomainName;
use Lettr\ValueObjects\Timestamp;

/**
 * Full domain details.
 */
final readonly class DomainDetail
{
    public function __construct(
        public DomainName $domain,
        public DomainStatus $status,
        public bool $canSend,
        public DnsStatus $dkimStatus,
        public DnsStatus $returnPathStatus,
        public Timestamp $createdAt,
        public ?Timestamp $verifiedAt,
        public ?string $trackingDomain,
        public DomainDns $dns,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     domain: string,
     *     status: string,
     *     can_send: bool,
     *     dkim_status: string,
     *     return_path_status: string,
     *     created_at: string,
     *     verified_at?: string|null,
     *     tracking_domain?: string|null,
     *     dns: array{
     *         return_path_host: string,
     *         return_path_value: string,
     *         dkim?: array{selector: string, public_key: string, headers: string}|null,
     *     },
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            domain: new DomainName($data['domain']),
            status: DomainStatus::from($data['status']),
            canSend: $data['can_send'],
            dkimStatus: DnsStatus::from($data['dkim_status']),
            returnPathStatus: DnsStatus::from($data['return_path_status']),
            createdAt: Timestamp::fromString($data['created_at']),
            verifiedAt: isset($data['verified_at']) ? Timestamp::fromString($data['verified_at']) : null,
            trackingDomain: $data['tracking_domain'] ?? null,
            dns: DomainDns::from($data['dns']),
        );
    }

    /**
     * Check if the domain is fully verified.
     */
    public function isVerified(): bool
    {
        return $this->status === DomainStatus::Approved
            && $this->dkimStatus === DnsStatus::Valid
            && $this->returnPathStatus === DnsStatus::Valid;
    }
}
