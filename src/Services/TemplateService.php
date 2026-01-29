<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Template\ListTemplatesFilter;
use Lettr\Dto\Template\TemplateDetail;
use Lettr\Responses\ListTemplatesResponse;

/**
 * Service for managing templates via the Lettr API.
 */
final class TemplateService
{
    private const TEMPLATES_ENDPOINT = 'templates';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * List templates with optional filtering.
     */
    public function list(?ListTemplatesFilter $filter = null): ListTemplatesResponse
    {
        $query = $filter?->toArray() ?? [];

        /**
         * @var array{
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
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::TEMPLATES_ENDPOINT, $query);

        return ListTemplatesResponse::from($response);
    }

    /**
     * Get template details by slug.
     */
    public function get(string $slug, ?int $projectId = null): TemplateDetail
    {
        $query = [];
        if ($projectId !== null) {
            $query['project_id'] = $projectId;
        }

        /**
         * @var array{
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
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::TEMPLATES_ENDPOINT.'/'.$slug, $query);

        return TemplateDetail::from($response);
    }
}
