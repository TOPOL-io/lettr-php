<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DnsStatus;
use Lettr\Enums\DomainStatus;
use Lettr\ValueObjects\DomainName;
use Lettr\ValueObjects\Timestamp;

/**
 * Response from creating a domain.
 */
final readonly class CreateDomainResponse
{
    public function __construct(
        public DomainName $domain,
        public DomainStatus $status,
        public Timestamp $createdAt,
        public DomainDns $dns,
        public DnsStatus $dkimStatus,
        public DnsStatus $returnPathStatus,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     domain: string,
     *     status: string,
     *     created_at: string,
     *     dkim_status: string,
     *     return_path_status: string,
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
            createdAt: Timestamp::fromString($data['created_at']),
            dns: DomainDns::from($data['dns']),
            dkimStatus: DnsStatus::from($data['dkim_status']),
            returnPathStatus: DnsStatus::from($data['return_path_status']),
        );
    }
}
