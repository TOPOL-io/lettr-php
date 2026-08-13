<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

use Lettr\Contracts\Arrayable;
use Lettr\Exceptions\InvalidValueException;

/**
 * Request body for bulk creating up to 1000 audience contacts.
 *
 * Two shapes are supported, and exactly one of them must be filled in:
 *
 * - `$emails` — a flat list of addresses that all share `$listId`/`$listIds`,
 *   `$properties` and `$topics`. This is the original shape and is unchanged.
 * - `$contacts` — one {@see BulkAudienceContactRow} per contact, each with its
 *   own properties, lists and topic subscriptions. Batch-wide `$listIds` and
 *   `$topics` are unioned into every row; a row-level property key or `opt_out`
 *   wins over the batch-wide value.
 *
 * Emails are normalized and deduplicated server-side. Existing addresses are
 * reported in the response's `alreadyExisted` count and are only modified when
 * `$updateExisting` is set — though they are attached to the requested lists
 * and opt-in topics either way.
 *
 * @see BulkStoreAudienceContactsResult for how partial failures are reported.
 */
final readonly class BulkCreateAudienceContactsData implements Arrayable
{
    /**
     * Prefer the {@see self::forEmails()} and {@see self::forContacts()} named
     * constructors — they make the chosen shape explicit.
     *
     * @param  array<int, string>  $emails  1–1000 addresses. Leave empty when using `$contacts`.
     * @param  array<string, string>|null  $properties  Applied to every contact in the batch.
     * @param  array<int, BulkAudienceContactRow>|null  $contacts  1–1000 rows. Alternative to `$emails`.
     * @param  array<int, string>|null  $listIds  Max 50. Batch-wide lists, on top of `$listId`.
     * @param  array<int, AudienceTopicSubscription>|null  $topics  Max 50. Batch-wide topic subscriptions.
     * @param  bool  $updateExisting  When `true`, existing contacts have their properties merged
     *                                (submitted keys overwrite, absent keys are preserved) and
     *                                `opt_out` entries applied.
     *
     * @throws InvalidValueException when neither `$emails` nor `$contacts` is provided.
     */
    public function __construct(
        public array $emails = [],
        public ?string $listId = null,
        public ?array $properties = null,
        public ?array $contacts = null,
        public ?array $listIds = null,
        public ?array $topics = null,
        public bool $updateExisting = false,
    ) {
        if ($this->emails === [] && ($this->contacts === null || $this->contacts === [])) {
            throw new InvalidValueException(
                'A bulk contact create needs at least one entry in either $emails or $contacts.'
            );
        }
    }

    /**
     * Build the flat shape: addresses that all share the same lists, properties
     * and topic subscriptions.
     *
     * @param  array<int, string>  $emails
     * @param  array<string, string>|null  $properties
     * @param  array<int, string>|null  $listIds
     * @param  array<int, AudienceTopicSubscription>|null  $topics
     */
    public static function forEmails(
        array $emails,
        ?string $listId = null,
        ?array $properties = null,
        ?array $listIds = null,
        ?array $topics = null,
        bool $updateExisting = false,
    ): self {
        return new self(
            emails: $emails,
            listId: $listId,
            properties: $properties,
            listIds: $listIds,
            topics: $topics,
            updateExisting: $updateExisting,
        );
    }

    /**
     * Build the per-contact shape: each row carries its own properties, lists
     * and topic subscriptions.
     *
     * @param  array<int, BulkAudienceContactRow>  $contacts
     * @param  array<int, string>|null  $listIds  Applied to every row on top of its own `listIds`.
     * @param  array<int, AudienceTopicSubscription>|null  $topics  Applied to every row.
     * @param  array<string, string>|null  $properties  Defaults for every row; a row's own key wins.
     */
    public static function forContacts(
        array $contacts,
        ?array $listIds = null,
        ?array $topics = null,
        ?array $properties = null,
        bool $updateExisting = false,
    ): self {
        return new self(
            properties: $properties,
            contacts: $contacts,
            listIds: $listIds,
            topics: $topics,
            updateExisting: $updateExisting,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->emails !== []) {
            $payload['emails'] = array_values($this->emails);
        }

        if ($this->listId !== null) {
            $payload['list_id'] = $this->listId;
        }

        if ($this->properties !== null) {
            $payload['properties'] = $this->properties;
        }

        if ($this->contacts !== null) {
            $payload['contacts'] = array_map(
                static fn (BulkAudienceContactRow $row): array => $row->toArray(),
                array_values($this->contacts),
            );
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

        // Omitted when false so the legacy payload stays byte-identical; the
        // API defaults it to false anyway.
        if ($this->updateExisting) {
            $payload['update_existing'] = true;
        }

        return $payload;
    }
}
