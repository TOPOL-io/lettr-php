<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Collections\DomainCollection;
use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Domain\CreateDomainData;
use Lettr\Dto\Domain\CreateDomainResponse;
use Lettr\Dto\Domain\Domain;
use Lettr\Dto\Domain\DomainDetail;
use Lettr\Dto\Domain\DomainVerification;
use Lettr\ValueObjects\DomainName;

/**
 * Service for managing domains via the Lettr API.
 */
final class DomainService
{
    private const DOMAINS_ENDPOINT = 'domains';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * List all domains.
     */
    public function list(): DomainCollection
    {
        /**
         * @var array{
         *     domains: array<int, array{
         *         domain: string,
         *         status: string,
         *         can_send: bool,
         *         dkim_status: string,
         *         return_path_status: string,
         *         created_at: string,
         *         verified_at?: string|null,
         *     }>
         * } $response
         */
        $response = $this->transporter->get(self::DOMAINS_ENDPOINT);

        $domains = array_map(
            static fn (array $domain): Domain => Domain::from($domain),
            $response['domains']
        );

        return DomainCollection::from($domains);
    }

    /**
     * Create a new domain.
     */
    public function create(string|DomainName|CreateDomainData $domain): CreateDomainResponse
    {
        $data = match (true) {
            $domain instanceof CreateDomainData => $domain,
            $domain instanceof DomainName => CreateDomainData::from($domain),
            default => CreateDomainData::from($domain),
        };

        /**
         * @var array{
         *     domain: string,
         *     status: string,
         *     status_label: string,
         *     dkim?: array{public: string, selector: string, headers: string, signing_domain: string}|null,
         * } $response
         */
        $response = $this->transporter->post(self::DOMAINS_ENDPOINT, $data->toArray());

        return CreateDomainResponse::from($response);
    }

    /**
     * Get domain details.
     */
    public function get(string|DomainName $domain): DomainDetail
    {
        $domainName = $domain instanceof DomainName ? (string) $domain : $domain;

        /**
         * @var array{
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
         * }  $response
         */
        $response = $this->transporter->get(self::DOMAINS_ENDPOINT.'/'.$domainName);

        return DomainDetail::from($response);
    }

    /**
     * Delete a domain.
     */
    public function delete(string|DomainName $domain): void
    {
        $domainName = $domain instanceof DomainName ? (string) $domain : $domain;

        $this->transporter->delete(self::DOMAINS_ENDPOINT.'/'.$domainName);
    }

    /**
     * Verify a domain's DNS configuration.
     */
    public function verify(string|DomainName $domain): DomainVerification
    {
        $domainName = $domain instanceof DomainName ? (string) $domain : $domain;

        /**
         * @var array{
         *     domain: string,
         *     dkim_status: string,
         *     cname_status: string,
         *     dmarc_status: string,
         *     spf_status: string,
         *     ownership_verified: bool|null,
         *     is_primary_domain: bool,
         *     dkim_warning_level: int,
         *     cname_warning_level: int,
         *     dmarc_warning_level: int,
         *     spf_warning_level: int,
         *     dns?: array{
         *         dkim_record?: string|null,
         *         cname_record?: string|null,
         *         dmarc_record?: string|null,
         *         spf_record?: string|null,
         *         dkim_error?: string|null,
         *         cname_error?: string|null,
         *         dmarc_error?: string|null,
         *         spf_error?: string|null,
         *     }|null,
         *     dmarc?: array{
         *         is_valid: bool,
         *         status: string,
         *         found_at_domain?: string|null,
         *         record?: string|null,
         *         policy?: string|null,
         *         subdomain_policy?: string|null,
         *         error?: string|null,
         *         covered_by_parent_policy: bool,
         *     }|null,
         *     spf?: array{
         *         is_valid: bool,
         *         status: string,
         *         record?: string|null,
         *         error?: string|null,
         *         includes_sparkpost: bool,
         *     }|null,
         * } $response
         */
        $response = $this->transporter->post(
            self::DOMAINS_ENDPOINT.'/'.$domainName.'/verify',
            []
        );

        return DomainVerification::from($response);
    }
}
