<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

/**
 * DNS records and errors from domain verification.
 */
final readonly class VerificationDns
{
    public function __construct(
        public ?string $dkimRecord,
        public ?string $cnameRecord,
        public ?string $dmarcRecord,
        public ?string $spfRecord,
        public ?string $dkimError,
        public ?string $cnameError,
        public ?string $dmarcError,
        public ?string $spfError,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     dkim_record?: string|null,
     *     cname_record?: string|null,
     *     dmarc_record?: string|null,
     *     spf_record?: string|null,
     *     dkim_error?: string|null,
     *     cname_error?: string|null,
     *     dmarc_error?: string|null,
     *     spf_error?: string|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            dkimRecord: $data['dkim_record'] ?? null,
            cnameRecord: $data['cname_record'] ?? null,
            dmarcRecord: $data['dmarc_record'] ?? null,
            spfRecord: $data['spf_record'] ?? null,
            dkimError: $data['dkim_error'] ?? null,
            cnameError: $data['cname_error'] ?? null,
            dmarcError: $data['dmarc_error'] ?? null,
            spfError: $data['spf_error'] ?? null,
        );
    }
}
