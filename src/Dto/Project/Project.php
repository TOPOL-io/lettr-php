<?php

declare(strict_types=1);

namespace Lettr\Dto\Project;

use Lettr\ValueObjects\Timestamp;

/**
 * Project list item.
 */
final readonly class Project
{
    public function __construct(
        public int $id,
        public string $name,
        public string $emoji,
        public int $teamId,
        public Timestamp $createdAt,
        public Timestamp $updatedAt,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     id: int,
     *     name: string,
     *     emoji: string,
     *     team_id: int,
     *     created_at: string,
     *     updated_at: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            emoji: $data['emoji'],
            teamId: $data['team_id'],
            createdAt: Timestamp::fromString($data['created_at']),
            updatedAt: Timestamp::fromString($data['updated_at']),
        );
    }
}
