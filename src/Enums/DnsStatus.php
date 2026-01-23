<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * DNS record verification status.
 */
enum DnsStatus: string
{
    case Valid = 'valid';
    case Unverified = 'unverified';
    case Invalid = 'invalid';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Valid => 'Valid',
            self::Unverified => 'Unverified',
            self::Invalid => 'Invalid',
        };
    }

    /**
     * Check if the DNS is properly configured.
     */
    public function isConfigured(): bool
    {
        return $this === self::Valid;
    }
}
