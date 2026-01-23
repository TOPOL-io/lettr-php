<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Contracts\Arrayable;
use Lettr\ValueObjects\DomainName;

/**
 * Data for creating a new domain.
 */
final readonly class CreateDomainData implements Arrayable
{
    public function __construct(
        public DomainName $domain,
    ) {}

    /**
     * Create from a string or DomainName.
     */
    public static function from(string|DomainName $domain): self
    {
        return new self(
            domain: $domain instanceof DomainName ? $domain : new DomainName($domain),
        );
    }

    /**
     * @return array{domain: string}
     */
    public function toArray(): array
    {
        return [
            'domain' => (string) $this->domain,
        ];
    }
}
