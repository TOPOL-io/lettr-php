<?php

declare(strict_types=1);

namespace Lettr\Dto\Folder;

use Lettr\Contracts\Arrayable;
use Lettr\Enums\TemplatePurpose;

/**
 * Filter parameters for listing folders.
 */
final readonly class ListFoldersFilter implements Arrayable
{
    public function __construct(
        public ?int $projectId = null,
        public ?TemplatePurpose $purpose = null,
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
     * Set the project ID.
     */
    public function projectId(int $projectId): self
    {
        return new self(
            projectId: $projectId,
            purpose: $this->purpose,
            perPage: $this->perPage,
            page: $this->page,
        );
    }

    /**
     * Narrow the list to one module. Absent means both.
     */
    public function purpose(TemplatePurpose $purpose): self
    {
        return new self(
            projectId: $this->projectId,
            purpose: $purpose,
            perPage: $this->perPage,
            page: $this->page,
        );
    }

    /**
     * Set items per page.
     */
    public function perPage(int $perPage): self
    {
        return new self(
            projectId: $this->projectId,
            purpose: $this->purpose,
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
            projectId: $this->projectId,
            purpose: $this->purpose,
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

        if ($this->projectId !== null) {
            $params['project_id'] = $this->projectId;
        }

        if ($this->purpose !== null) {
            $params['purpose'] = $this->purpose->value;
        }

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
        return $this->projectId !== null
            || $this->purpose !== null
            || $this->perPage !== null
            || $this->page !== null;
    }
}
