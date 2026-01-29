<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\Dto\Template\MergeTag;

/**
 * Response from the get merge tags endpoint.
 */
final readonly class GetMergeTagsResponse
{
    /**
     * @param  array<int, MergeTag>  $mergeTags
     */
    public function __construct(
        public int $projectId,
        public string $templateSlug,
        public int $version,
        public array $mergeTags,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     project_id: int,
     *     template_slug: string,
     *     version: int,
     *     merge_tags: array<int, array{key: string, required: bool, type?: string|null, children?: array<int, array{key: string, type?: string|null}>|null}>,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            projectId: $data['project_id'],
            templateSlug: $data['template_slug'],
            version: $data['version'],
            mergeTags: array_map(
                static fn (array $tag): MergeTag => MergeTag::from($tag),
                $data['merge_tags'],
            ),
        );
    }
}
