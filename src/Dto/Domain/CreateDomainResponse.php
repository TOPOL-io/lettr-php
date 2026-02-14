<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DomainStatus;
use Lettr\ValueObjects\DomainName;

/**
 * Response from creating a domain.
 */
final readonly class CreateDomainResponse
{
    public function __construct(
        public DomainName $domain,
        public DomainStatus $status,
        public string $statusLabel,
        public ?DomainDkim $dkim,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     domain: string,
     *     status: string,
     *     status_label: string,
     *     dkim?: array{public: string, selector: string, headers: string, signing_domain: string}|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            domain: new DomainName($data['domain']),
            status: DomainStatus::from($data['status']),
            statusLabel: $data['status_label'],
            dkim: isset($data['dkim']) ? DomainDkim::from($data['dkim']) : null,
        );
    }
}
