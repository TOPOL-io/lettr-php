<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

/**
 * DKIM configuration for a domain.
 */
final readonly class DomainDkim
{
    public function __construct(
        public string $selector,
        public string $publicKey,
        public string $headers,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     selector: string,
     *     public_key: string,
     *     headers: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            selector: $data['selector'],
            publicKey: $data['public_key'],
            headers: $data['headers'],
        );
    }

    /**
     * Get the DNS record name for DKIM.
     */
    public function recordName(string $domain): string
    {
        return sprintf('%s._domainkey.%s', $this->selector, $domain);
    }

    /**
     * Get the DNS record value.
     */
    public function recordValue(): string
    {
        return sprintf('v=DKIM1; k=rsa; h=%s; p=%s', $this->headers, $this->publicKey);
    }
}
