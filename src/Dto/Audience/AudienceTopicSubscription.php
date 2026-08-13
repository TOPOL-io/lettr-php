<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

use Lettr\Contracts\Arrayable;
use Lettr\Enums\AudienceTopicSubscriptionState;

/**
 * A topic and the subscription state to apply to it.
 *
 * Used both batch-wide on {@see BulkCreateAudienceContactsData} and per row on
 * {@see BulkAudienceContactRow}. A row-level `opt_out` wins over a batch-level
 * `opt_in` for that contact.
 */
final readonly class AudienceTopicSubscription implements Arrayable
{
    public function __construct(
        public string $id,
        public AudienceTopicSubscriptionState $subscription = AudienceTopicSubscriptionState::OptIn,
    ) {}

    /**
     * Subscribe the contact to the topic.
     */
    public static function optIn(string $id): self
    {
        return new self($id, AudienceTopicSubscriptionState::OptIn);
    }

    /**
     * Suppress the topic for the contact — including a topic that would
     * otherwise auto-subscribe newly created contacts.
     */
    public static function optOut(string $id): self
    {
        return new self($id, AudienceTopicSubscriptionState::OptOut);
    }

    /**
     * @return array{id: string, subscription: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'subscription' => $this->subscription->value,
        ];
    }
}
