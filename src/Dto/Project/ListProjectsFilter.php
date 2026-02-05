<?php

declare(strict_types=1);

namespace Lettr\Dto\Project;

use Lettr\Contracts\Arrayable;

/**
 * Filter parameters for listing projects.
 */
final readonly class ListProjectsFilter implements Arrayable
{
    public function __construct(
        public ?int $perPage = null,
        public ?int $page = null,
    ) {}

    /**
     * Create a new filter.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Set items per page.
     */
    public function perPage(int $perPage): self
    {
        return new self(
            perPage: $perPage,
            page: $this->page,
        );
    }

    /**
     * Set the page number.
     */
    public function page(int $page): self
    {
        return new self(
            perPage: $this->perPage,
            page: $page,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->perPage !== null) {
            $params['per_page'] = $this->perPage;
        }

        if ($this->page !== null) {
            $params['page'] = $this->page;
        }

        return $params;
    }

    /**
     * Check if any filters are set.
     */
    public function hasFilters(): bool
    {
        return $this->perPage !== null
            || $this->page !== null;
    }
}
