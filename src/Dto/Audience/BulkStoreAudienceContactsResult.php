<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

/**
 * Result of a bulk contact create call.
 *
 * A bulk create can partially succeed: rows that fail validation are skipped
 * and reported in `$errors`, while the rest of the batch is still written. The
 * call returns HTTP 201 either way, so **do not read a successful call as
 * "everything landed"** — check {@see self::hasErrors()}.
 *
 * `$alreadyExisted` and `$updated` overlap by design. They answer different
 * questions ("was the address already in the audience?" vs "did this request
 * change the contact?"), so the counters do not sum to the row count: a
 * contact that already existed and got attached to a list is counted in both.
 */
final readonly class BulkStoreAudienceContactsResult
{
    /**
     * @param  int  $updated  Existing contacts this request changed — properties merged,
     *                        a list or topic attached, or a subscription dropped.
     * @param  int  $errorCount  Number of skipped rows.
     * @param  array<int, BulkAudienceContactError>  $errors  The skipped rows.
     * @param  array<int, BulkAudienceContactRef>  $contacts  Every contact that exists after the
     *                                                        request, in submission order.
     */
    public function __construct(
        public int $created,
        public int $alreadyExisted,
        public int $updated = 0,
        public int $errorCount = 0,
        public array $errors = [],
        public array $contacts = [],
    ) {}

    /**
     * @param  array{
     *     created: int,
     *     already_existed: int,
     *     updated?: int,
     *     error_count?: int,
     *     errors?: array<int, array{index: int, email: string|null, error_code: string, error: string}>,
     *     contacts?: array<int, array{id: string, email: string, created: bool}>,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            created: $data['created'],
            alreadyExisted: $data['already_existed'],
            updated: $data['updated'] ?? 0,
            errorCount: $data['error_count'] ?? 0,
            errors: array_map(
                static fn (array $error): BulkAudienceContactError => BulkAudienceContactError::from($error),
                $data['errors'] ?? [],
            ),
            contacts: array_map(
                static fn (array $contact): BulkAudienceContactRef => BulkAudienceContactRef::from($contact),
                $data['contacts'] ?? [],
            ),
        );
    }

    /**
     * Whether any row was skipped. Always check this — a bulk create reports
     * partial failures in the body, not in the HTTP status.
     */
    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * The ids of every contact that exists after the request, in submission
     * order — ready to feed into the bulk list and topic endpoints.
     *
     * @return array<int, string>
     */
    public function contactIds(): array
    {
        return array_map(
            static fn (BulkAudienceContactRef $contact): string => $contact->id,
            $this->contacts,
        );
    }

    /**
     * Look up the id for a submitted address. Matching is case-insensitive
     * because the API normalizes addresses before storing them.
     */
    public function idFor(string $email): ?string
    {
        $needle = mb_strtolower(trim($email));

        foreach ($this->contacts as $contact) {
            if (mb_strtolower($contact->email) === $needle) {
                return $contact->id;
            }
        }

        return null;
    }
}
