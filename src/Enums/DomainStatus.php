<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * Domain verification status.
 */
enum DomainStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Blocked = 'blocked';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Blocked => 'Blocked',
        };
    }

    /**
     * Check if the domain can send emails.
     */
    public function canSend(): bool
    {
        return $this === self::Approved;
    }
}
