<?php

declare(strict_types=1);

namespace Lettr\Dto\Template;

/**
 * A single merge tag from a template.
 */
final readonly class MergeTag
{
    /**
     * @param  array<int, MergeTagChild>|null  $children
     */
    public function __construct(
        public string $key,
        public bool $required,
        public ?string $type = null,
        public ?array $children = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     key: string,
     *     required: bool,
     *     type?: string|null,
     *     children?: array<int, array{key: string, type?: string|null}>|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        $children = null;
        if (isset($data['children'])) {
            $children = array_map(
                static fn (array $child): MergeTagChild => MergeTagChild::from($child),
                $data['children'],
            );
        }

        return new self(
            key: $data['key'],
            required: $data['required'],
            type: $data['type'] ?? null,
            children: $children,
        );
    }
}
