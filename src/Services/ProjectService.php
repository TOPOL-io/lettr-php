<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Project\ListProjectsFilter;
use Lettr\Responses\ListProjectsResponse;

/**
 * Service for managing projects via the Lettr API.
 */
final class ProjectService
{
    private const PROJECTS_ENDPOINT = 'projects';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * List projects with optional filtering.
     */
    public function list(?ListProjectsFilter $filter = null): ListProjectsResponse
    {
        $query = $filter?->toArray() ?? [];

        /**
         * @var array{
         *     projects: array<int, array{
         *         id: int,
         *         name: string,
         *         emoji: string,
         *         team_id: int,
         *         created_at: string,
         *         updated_at: string,
         *     }>,
         *     pagination: array{current_page: int, last_page: int, per_page: int, total: int},
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::PROJECTS_ENDPOINT, $query);

        return ListProjectsResponse::from($response);
    }
}
