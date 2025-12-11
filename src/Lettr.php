<?php

declare(strict_types=1);

namespace Lettr;

use Lettr\Contracts\TransporterContract;
use Lettr\Services\EmailService;

/**
 * Lettr SDK entry point.
 *
 * @property-read EmailService $emails
 */
final class Lettr
{
    /**
     * The current SDK version.
     */
    public const VERSION = '1.0.0';

    /**
     * The default API base URL.
     */
    public const DEFAULT_BASE_URL = 'https://app.uselettr.com';

    private ?EmailService $emailService = null;

    public function __construct(
        private readonly TransporterContract $client,
    ) {}

    /**
     * Create a new Lettr instance with the given API key.
     */
    public static function client(string $apiKey, ?string $baseUrl = null): self
    {
        return new self(new Client($apiKey, $baseUrl));
    }

    /**
     * Get the email service.
     */
    public function emails(): EmailService
    {
        if ($this->emailService === null) {
            $this->emailService = new EmailService($this->client);
        }

        return $this->emailService;
    }

    /**
     * Magic method to access services as properties.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'emails' => $this->emails(),
            default => throw new \InvalidArgumentException("Unknown service: {$name}"),
        };
    }
}
