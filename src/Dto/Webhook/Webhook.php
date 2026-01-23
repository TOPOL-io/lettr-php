<?php

declare(strict_types=1);

namespace Lettr\Dto\Webhook;

use Lettr\Collections\EventTypeCollection;
use Lettr\Enums\EventType;
use Lettr\Enums\WebhookAuthType;
use Lettr\Enums\WebhookStatus;
use Lettr\ValueObjects\Timestamp;
use Lettr\ValueObjects\WebhookId;

/**
 * Webhook configuration.
 */
final readonly class Webhook
{
    public function __construct(
        public WebhookId $id,
        public string $name,
        public string $url,
        public bool $enabled,
        public WebhookAuthType $authType,
        public EventTypeCollection $eventTypes,
        public Timestamp $createdAt,
        public ?WebhookStatus $lastStatus = null,
        public ?Timestamp $lastTriggeredAt = null,
        public ?string $lastError = null,
        public ?Timestamp $updatedAt = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     id: string,
     *     name: string,
     *     url: string,
     *     enabled: bool,
     *     auth_type: string,
     *     event_types: array<string>,
     *     last_status?: string|null,
     *     last_triggered_at?: string|null,
     *     last_error?: string|null,
     *     created_at: string,
     *     updated_at?: string|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: new WebhookId($data['id']),
            name: $data['name'],
            url: $data['url'],
            enabled: $data['enabled'],
            authType: WebhookAuthType::from($data['auth_type']),
            eventTypes: EventTypeCollection::from(
                array_map(static fn (string $type): EventType => EventType::from($type), $data['event_types'])
            ),
            createdAt: Timestamp::fromString($data['created_at']),
            lastStatus: isset($data['last_status']) ? WebhookStatus::from($data['last_status']) : null,
            lastTriggeredAt: isset($data['last_triggered_at']) ? Timestamp::fromString($data['last_triggered_at']) : null,
            lastError: $data['last_error'] ?? null,
            updatedAt: isset($data['updated_at']) ? Timestamp::fromString($data['updated_at']) : null,
        );
    }

    /**
     * Check if the webhook is currently working.
     */
    public function isHealthy(): bool
    {
        return $this->enabled
            && ($this->lastStatus === null || $this->lastStatus === WebhookStatus::Success);
    }

    /**
     * Check if the webhook is failing.
     */
    public function isFailing(): bool
    {
        return $this->lastStatus === WebhookStatus::Failure;
    }

    /**
     * Check if the webhook listens to a specific event type.
     */
    public function listensTo(EventType $type): bool
    {
        return $this->eventTypes->contains($type);
    }
}
