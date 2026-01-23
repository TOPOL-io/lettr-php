<?php

declare(strict_types=1);

namespace Lettr;

use Lettr\Contracts\TransporterContract;
use Lettr\Services\DomainService;
use Lettr\Services\EmailService;
use Lettr\Services\WebhookService;

/**
 * Lettr SDK entry point.
 *
 * @property-read EmailService $emails
 * @property-read DomainService $domains
 * @property-read WebhookService $webhooks
 */
final class Lettr
{
    /**
     * The current SDK version.
     */
    public const VERSION = '0.1.0';

    /**
     * The API base URL.
     */
    public const BASE_URL = 'https://app.lettr.com/api';

    private ?EmailService $emailService = null;

    private ?DomainService $domainService = null;

    private ?WebhookService $webhookService = null;

    public function __construct(
        private readonly TransporterContract $client,
    ) {}

    /**
     * Create a new Lettr instance with the given API key.
     */
    public static function client(string $apiKey): self
    {
        return new self(new Client($apiKey));
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
     * Get the domain service.
     */
    public function domains(): DomainService
    {
        if ($this->domainService === null) {
            $this->domainService = new DomainService($this->client);
        }

        return $this->domainService;
    }

    /**
     * Get the webhook service.
     */
    public function webhooks(): WebhookService
    {
        if ($this->webhookService === null) {
            $this->webhookService = new WebhookService($this->client);
        }

        return $this->webhookService;
    }

    /**
     * Magic method to access services as properties.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'emails' => $this->emails(),
            'domains' => $this->domains(),
            'webhooks' => $this->webhooks(),
            default => throw new \InvalidArgumentException("Unknown service: {$name}"),
        };
    }
}
