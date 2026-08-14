<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

use Lettr\Contracts\Arrayable;

/**
 * One contact in a bulk create payload.
 *
 * Lists and topics set here are applied **on top of** the batch-wide ones on
 * {@see BulkCreateAudienceContactsData}; properties set here override the
 * batch-wide value for the same key.
 *
 * A row that fails validation is skipped rather than failing the request — it
 * comes back in {@see BulkStoreAudienceContactsResult::$errors}.
 */
final readonly class BulkAudienceContactRow implements Arrayable
{
    /**
     * @param  array<string, string>|null  $properties  Each key must match a property defined for the team.
     * @param  array<int, string>|null  $listIds  Max 50.
     * @param  array<int, AudienceTopicSubscription>|null  $topics  Max 50.
     */
    public function __construct(
        public string $email,
        public ?array $properties = null,
        public ?array $listIds = null,
        public ?array $topics = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'email' => $this->email,
        ];

        if ($this->properties !== null) {
            $payload['properties'] = $this->properties;
        }

        if ($this->listIds !== null) {
            $payload['list_ids'] = array_values($this->listIds);
        }

        if ($this->topics !== null) {
            $payload['topics'] = array_map(
                static fn (AudienceTopicSubscription $topic): array => $topic->toArray(),
                array_values($this->topics),
            );
        }

        return $payload;
    }
}
