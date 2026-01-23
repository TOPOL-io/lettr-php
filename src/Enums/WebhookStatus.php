<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * Webhook delivery status.
 */
enum WebhookStatus: string
{
    case Success = 'success';
    case Failure = 'failure';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Success => 'Success',
            self::Failure => 'Failure',
        };
    }

    /**
     * Check if the webhook was delivered successfully.
     */
    public function isSuccessful(): bool
    {
        return $this === self::Success;
    }
}
