<?php

declare(strict_types=1);

namespace Lettr\Dto\Domain;

use Lettr\Enums\DomainStatus;
use Lettr\ValueObjects\DomainName;

/**
 * Domain verification result.
 */
final readonly class DomainVerification
{
    public function __construct(
        public DomainName $domain,
        public DomainStatus $status,
        public bool $canSend,
        public DomainDnsVerification $dkim,
        public DomainDnsVerification $returnPath,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     domain: string,
     *     status: string,
     *     can_send: bool,
     *     dkim: array{status: string, error?: string|null, expected?: string|null, found?: string|null},
     *     return_path: array{status: string, error?: string|null, expected?: string|null, found?: string|null},
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            domain: new DomainName($data['domain']),
            status: DomainStatus::from($data['status']),
            canSend: $data['can_send'],
            dkim: DomainDnsVerification::from($data['dkim']),
            returnPath: DomainDnsVerification::from($data['return_path']),
        );
    }

    /**
     * Check if verification passed completely.
     */
    public function isFullyVerified(): bool
    {
        return $this->dkim->isValid() && $this->returnPath->isValid();
    }

    /**
     * Check if there are any verification errors.
     */
    public function hasErrors(): bool
    {
        return $this->dkim->hasError() || $this->returnPath->hasError();
    }

    /**
     * Get all error messages.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        $errors = [];

        if ($this->dkim->hasError() && $this->dkim->error !== null) {
            $errors['dkim'] = $this->dkim->error;
        }

        if ($this->returnPath->hasError() && $this->returnPath->error !== null) {
            $errors['return_path'] = $this->returnPath->error;
        }

        return $errors;
    }
}
