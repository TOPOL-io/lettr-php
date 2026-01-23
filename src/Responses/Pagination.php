<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\ValueObjects\Cursor;

/**
 * Pagination information for list responses.
 */
final readonly class Pagination
{
    public function __construct(
        public ?Cursor $nextCursor,
        public int $perPage,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     next_cursor?: string|null,
     *     per_page: int,
     * }  $data
     */
    public static function from(array $data): self
    {
        $cursor = $data['next_cursor'] ?? null;

        return new self(
            nextCursor: is_string($cursor) ? new Cursor($cursor) : null,
            perPage: $data['per_page'],
        );
    }

    /**
     * Check if there's a next page.
     */
    public function hasNextPage(): bool
    {
        return $this->nextCursor !== null;
    }
}
