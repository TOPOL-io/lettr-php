<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\Collections\FolderCollection;
use Lettr\Dto\Folder\Folder;

/**
 * Response from listing folders.
 */
final readonly class ListFoldersResponse
{
    public function __construct(
        public FolderCollection $folders,
        public FolderPagination $pagination,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
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
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            folders: FolderCollection::from(
                array_map(
                    static fn (array $folder): Folder => Folder::from($folder),
                    $data['folders']
                )
            ),
            pagination: FolderPagination::from($data['pagination']),
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
