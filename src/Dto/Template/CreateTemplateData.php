<?php

declare(strict_types=1);

namespace Lettr\Dto\Template;

use Lettr\Contracts\Arrayable;

/**
 * Data Transfer Object for creating a template.
 */
final readonly class CreateTemplateData implements Arrayable
{
    public function __construct(
        public string $name,
        public ?string $slug = null,
        public ?int $projectId = null,
        public ?int $folderId = null,
        public ?string $html = null,
        public ?string $json = null,
    ) {}

    /**
     * Convert the DTO to an array for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->slug !== null) {
            $data['slug'] = $this->slug;
        }

        if ($this->projectId !== null) {
            $data['project_id'] = $this->projectId;
        }

        if ($this->folderId !== null) {
            $data['folder_id'] = $this->folderId;
        }

        if ($this->html !== null) {
            $data['html'] = $this->html;
        }

        if ($this->json !== null) {
            $data['json'] = $this->json;
        }

        return $data;
    }
}
