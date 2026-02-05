<?php

declare(strict_types=1);

namespace Lettr\Dto;

use Lettr\ValueObjects\Timestamp;

/**
 * Auth check response.
 */
final readonly class AuthStatus
{
    public function __construct(
        public int $teamId,
        public Timestamp $timestamp,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{team_id: int, timestamp: string}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            teamId: $data['team_id'],
            timestamp: Timestamp::fromString($data['timestamp']),
        );
    }
}
