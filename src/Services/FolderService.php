<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Folder\ListFoldersFilter;
use Lettr\Responses\ListFoldersResponse;

/**
 * Service for reading template folders via the Lettr API.
 *
 * Read-only: creating, renaming and deleting folders stay in the app, because
 * deleting one moves or deletes the templates inside it.
 */
final class FolderService
{
    private const FOLDERS_ENDPOINT = 'folders';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * List folders with optional filtering.
     *
     * Without a `projectId` the team's default project is used, the same way
     * {@see TemplateService::list()} resolves it.
     */
    public function list(?ListFoldersFilter $filter = null): ListFoldersResponse
    {
        $query = $filter?->toArray() ?? [];

        /**
         * @var array{
         *     folders: array<int, array{
         *         id: int,
         *         name: string,
         *         project_id: int,
         *         purpose?: string|null,
         *         templates_count?: int,
         *         created_at: string,
         *         updated_at: string,
         *     }>,
         *     pagination: array{current_page: int, last_page: int, per_page: int, total: int},
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::FOLDERS_ENDPOINT, $query);

        return ListFoldersResponse::from($response);
    }
}
