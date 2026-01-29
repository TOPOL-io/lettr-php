<?php

declare(strict_types=1);

namespace Lettr\Dto\Template;

use Lettr\ValueObjects\Timestamp;

/**
 * Template list item.
 */
final readonly class Template
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $projectId,
        public ?int $folderId,
        public Timestamp $createdAt,
        public Timestamp $updatedAt,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     id: int,
     *     name: string,
     *     slug: string,
     *     project_id: int,
     *     folder_id?: int|null,
     *     created_at: string,
     *     updated_at: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            projectId: $data['project_id'],
            folderId: $data['folder_id'] ?? null,
            createdAt: Timestamp::fromString($data['created_at']),
            updatedAt: Timestamp::fromString($data['updated_at']),
        );
    }
}
