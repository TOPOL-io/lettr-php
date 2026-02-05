<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\Collections\ProjectCollection;
use Lettr\Dto\Project\Project;

/**
 * Response from listing projects.
 */
final readonly class ListProjectsResponse
{
    public function __construct(
        public ProjectCollection $projects,
        public ProjectPagination $pagination,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     projects: array<int, array{
     *         id: int,
     *         name: string,
     *         emoji: string,
     *         team_id: int,
     *         created_at: string,
     *         updated_at: string,
     *     }>,
     *     pagination: array{current_page: int, last_page: int, per_page: int, total: int},
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            projects: ProjectCollection::from(
                array_map(
                    static fn (array $project): Project => Project::from($project),
                    $data['projects']
                )
            ),
            pagination: ProjectPagination::from($data['pagination']),
        );
    }

    /**
     * Check if there are more pages.
     */
    public function hasMore(): bool
    {
        return $this->pagination->hasNextPage();
    }
}
