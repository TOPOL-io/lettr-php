<?php

declare(strict_types=1);

namespace Lettr\Dto\Template;

use Lettr\ValueObjects\Timestamp;

/**
 * Full template details.
 */
final readonly class TemplateDetail
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $projectId,
        public ?int $folderId,
        public ?int $activeVersion,
        public int $versionsCount,
        public ?string $html,
        public ?string $json,
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
     *     active_version: int|null,
     *     versions_count: int,
     *     html: string|null,
     *     json?: string|null,
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
            activeVersion: $data['active_version'],
            versionsCount: $data['versions_count'],
            html: $data['html'],
            json: $data['json'] ?? null,
            createdAt: Timestamp::fromString($data['created_at']),
            updatedAt: Timestamp::fromString($data['updated_at']),
        );
    }
}
