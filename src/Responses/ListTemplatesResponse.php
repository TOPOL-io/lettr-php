<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\Collections\TemplateCollection;
use Lettr\Dto\Template\Template;

/**
 * Response from listing templates.
 */
final readonly class ListTemplatesResponse
{
    public function __construct(
        public TemplateCollection $templates,
        public TemplatePagination $pagination,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     templates: array<int, array{
     *         id: int,
     *         name: string,
     *         slug: string,
     *         project_id: int,
     *         folder_id?: int|null,
     *         created_at: string,
     *         updated_at: string,
     *     }>,
     *     pagination: array{current_page: int, last_page: int, per_page: int, total: int},
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            templates: TemplateCollection::from(
                array_map(
                    static fn (array $template): Template => Template::from($template),
                    $data['templates']
                )
            ),
            pagination: TemplatePagination::from($data['pagination']),
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
