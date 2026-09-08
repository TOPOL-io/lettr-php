<?php

declare(strict_types=1);

namespace Lettr\Dto\Folder;

use Lettr\Enums\TemplatePurpose;
use Lettr\ValueObjects\Timestamp;

/**
 * Folder list item.
 *
 * `$id` is what `CreateTemplateData::$folderId` expects, so listing folders is
 * how a caller picks where a template lands without hardcoding an integer read
 * out of an app URL.
 */
final readonly class Folder
{
    public function __construct(
        public int $id,
        public string $name,
        public int $projectId,
        public TemplatePurpose $purpose,
        public int $templatesCount,
        public Timestamp $createdAt,
        public Timestamp $updatedAt,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     id: int,
     *     name: string,
     *     project_id: int,
     *     purpose?: string|null,
     *     templates_count?: int,
     *     created_at: string,
     *     updated_at: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            projectId: $data['project_id'],
            purpose: TemplatePurpose::tryFrom((string) ($data['purpose'] ?? '')) ?? TemplatePurpose::Transactional,
            templatesCount: $data['templates_count'] ?? 0,
            createdAt: Timestamp::fromString($data['created_at']),
            updatedAt: Timestamp::fromString($data['updated_at']),
        );
    }
}
