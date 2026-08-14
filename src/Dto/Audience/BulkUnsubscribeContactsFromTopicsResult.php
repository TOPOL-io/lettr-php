<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

/**
 * Result of a bulk contact-from-topics unsubscribe call.
 *
 * Pairs that did not exist are ignored, so `$unsubscribed` can be lower than
 * `$totalPairs`.
 */
final readonly class BulkUnsubscribeContactsFromTopicsResult
{
    public function __construct(
        public int $unsubscribed,
        public int $totalPairs,
    ) {}

    /**
     * @param  array{unsubscribed: int, total_pairs: int}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            unsubscribed: $data['unsubscribed'],
            totalPairs: $data['total_pairs'],
        );
    }
}
