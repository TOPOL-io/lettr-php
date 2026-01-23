<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

/**
 * DNS configuration for a domain.
 */
final readonly class DomainDns
{
    public function __construct(
        public string $returnPathHost,
        public string $returnPathValue,
        public ?DomainDkim $dkim = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     return_path_host: string,
     *     return_path_value: string,
     *     dkim?: array{selector: string, public_key: string, headers: string}|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            returnPathHost: $data['return_path_host'],
            returnPathValue: $data['return_path_value'],
            dkim: isset($data['dkim']) ? DomainDkim::from($data['dkim']) : null,
        );
    }
}
