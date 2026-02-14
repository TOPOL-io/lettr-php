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
        public ?string $signingDomain = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     selector: string,
     *     public_key?: string,
     *     public?: string,
     *     headers: string,
     *     signing_domain?: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        $publicKey = $data['public_key'] ?? $data['public'] ?? '';

        return new self(
            selector: $data['selector'],
            publicKey: $publicKey,
            headers: $data['headers'],
            signingDomain: $data['signing_domain'] ?? null,
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
