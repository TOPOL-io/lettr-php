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
        public DnsStatus $cnameStatus,
        public DnsStatus $dkimStatus,
        public DnsStatus $dmarcStatus,
        public Timestamp $createdAt,
        public ?Timestamp $verifiedAt,
        public ?string $trackingDomain,
        public ?DomainDkim $dkim,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     domain: string,
     *     status: string,
     *     can_send: bool,
     *     dkim_status: string,
     *     cname_status: string,
     *     dmarc_status: string,
     *     created_at: string,
     *     verified_at?: string|null,
     *     tracking_domain?: string|null,
     *     dns?: array{
     *         dkim: array{selector: string, public_key: string, headers: string},
     *     },
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            domain: new DomainName($data['domain']),
            status: DomainStatus::from($data['status']),
            canSend: $data['can_send'],
            cnameStatus: DnsStatus::from($data['cname_status']),
            dkimStatus: DnsStatus::from($data['dkim_status']),
            dmarcStatus: DnsStatus::from($data['dmarc_status']),
            createdAt: Timestamp::fromString($data['created_at']),
            verifiedAt: isset($data['verified_at']) ? Timestamp::fromString($data['verified_at']) : null,
            trackingDomain: $data['tracking_domain'] ?? null,
            dkim: isset($data['dns']) ? DomainDkim::from($data['dns']['dkim']) : null,
        );
    }

    /**
     * Check if the domain is fully verified.
     */
    public function isVerified(): bool
    {
        return $this->status === DomainStatus::Approved
            && $this->dkimStatus === DnsStatus::Valid
            && $this->cnameStatus === DnsStatus::Valid;
    }
}
