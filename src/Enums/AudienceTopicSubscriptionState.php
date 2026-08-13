<?php

declare(strict_types=1);

namespace Lettr\Enums;

/**
 * The subscription state to apply to a topic in a write request.
 *
 * - `OptIn`  — subscribe the contact to the topic.
 * - `OptOut` — suppress the topic. This also cancels the auto-subscription a
 *   topic whose default is {@see AudienceTopicDefaultSubscription::OptOut}
 *   would otherwise give a newly created contact, so both can be expressed in
 *   a single request. With `update_existing` enabled it also unsubscribes a
 *   contact that was already subscribed.
 *
 * Distinct from {@see AudienceTopicDefaultSubscription}, which describes how a
 * topic behaves for new contacts rather than what a request should do.
 */
enum AudienceTopicSubscriptionState: string
{
    case OptIn = 'opt_in';
    case OptOut = 'opt_out';

    public function label(): string
    {
        return match ($this) {
            self::OptIn => 'Opt-in',
            self::OptOut => 'Opt-out',
        };
    }

    public function isOptIn(): bool
    {
        return $this === self::OptIn;
    }

    public function isOptOut(): bool
    {
        return $this === self::OptOut;
    }
}
