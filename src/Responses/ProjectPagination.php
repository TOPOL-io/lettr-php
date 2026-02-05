<?php

declare(strict_types=1);

namespace Lettr\Responses;

/**
 * Pagination information for project list responses.
 */
final readonly class ProjectPagination
{
    public function __construct(
        public int $currentPage,
        public int $lastPage,
        public int $perPage,
        public int $total,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     current_page: int,
     *     last_page: int,
     *     per_page: int,
     *     total: int,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            currentPage: $data['current_page'],
            lastPage: $data['last_page'],
            perPage: $data['per_page'],
            total: $data['total'],
        );
    }

    /**
     * Check if there's a next page.
     */
    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    /**
     * Check if there's a previous page.
     */
    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    /**
     * Get the next page number.
     */
    public function nextPage(): ?int
    {
        return $this->hasNextPage() ? $this->currentPage + 1 : null;
    }

    /**
     * Get the previous page number.
     */
    public function previousPage(): ?int
    {
        return $this->hasPreviousPage() ? $this->currentPage - 1 : null;
    }
}
