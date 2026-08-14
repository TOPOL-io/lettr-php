<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

use Lettr\Contracts\Arrayable;

/**
 * Request body for subscribing or unsubscribing every (contact × topic)
 * combination in bulk (up to 1000 contacts × 50 topics).
 *
 * The same body serves both directions — see
 * `AudienceContactService::bulkSubscribeTopics()` and
 * `AudienceContactService::bulkUnsubscribeTopics()`.
 */
final readonly class BulkAudienceContactTopicsData implements Arrayable
{
    /**
     * @param  array<int, string>  $contactIds  1–1000 contact ids.
     * @param  array<int, string>  $topicIds  1–50 topic ids.
     */
    public function __construct(
        public array $contactIds,
        public array $topicIds,
    ) {}

    /**
     * @return array{contact_ids: array<int, string>, topic_ids: array<int, string>}
     */
    public function toArray(): array
    {
        return [
            'contact_ids' => array_values($this->contactIds),
            'topic_ids' => array_values($this->topicIds),
        ];
    }
}
