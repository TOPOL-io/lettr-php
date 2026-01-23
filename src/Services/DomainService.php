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
         *     created_at: string,
         *     dkim_status: string,
         *     return_path_status: string,
         *     dns: array{
         *         return_path_host: string,
         *         return_path_value: string,
         *         dkim?: array{selector: string, public_key: string, headers: string}|null,
         *     },
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
         *     return_path_status: string,
         *     created_at: string,
         *     verified_at?: string|null,
         *     tracking_domain?: string|null,
         *     dns: array{
         *         return_path_host: string,
         *         return_path_value: string,
         *         dkim?: array{selector: string, public_key: string, headers: string}|null,
         *     },
         * } $response
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
         *     status: string,
         *     can_send: bool,
         *     dkim: array{status: string, error?: string|null, expected?: string|null, found?: string|null},
         *     return_path: array{status: string, error?: string|null, expected?: string|null, found?: string|null},
         * } $response
         */
        $response = $this->transporter->post(
            self::DOMAINS_ENDPOINT.'/'.$domainName.'/verify',
            []
        );

        return DomainVerification::from($response);
    }
}
