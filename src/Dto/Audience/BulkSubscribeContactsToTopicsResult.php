<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

/**
 * Result of a bulk contact-to-topics subscribe call.
 */
final readonly class BulkSubscribeContactsToTopicsResult
{
    public function __construct(
        public int $subscribed,
        public int $alreadySubscribed,
        public int $totalPairs,
    ) {}

    /**
     * @param  array{subscribed: int, already_subscribed: int, total_pairs: int}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            subscribed: $data['subscribed'],
            alreadySubscribed: $data['already_subscribed'],
            totalPairs: $data['total_pairs'],
        );
    }
}
