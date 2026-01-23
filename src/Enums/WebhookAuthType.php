<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * Webhook authentication types.
 */
enum WebhookAuthType: string
{
    case None = 'none';
    case Basic = 'basic';
    case OAuth2 = 'oauth2';

    /**
     * Get a human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Basic => 'Basic Authentication',
            self::OAuth2 => 'OAuth 2.0',
        };
    }

    /**
     * Check if authentication is enabled.
     */
    public function hasAuth(): bool
    {
        return $this !== self::None;
    }
}
