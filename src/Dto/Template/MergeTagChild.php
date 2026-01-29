<?php

declare(strict_types=1);

namespace Lettr\Dto\Template;

/**
 * A child merge tag (nested within a parent merge tag).
 */
final readonly class MergeTagChild
{
    public function __construct(
        public string $key,
        public ?string $type = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{key: string, type?: string|null}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            key: $data['key'],
            type: $data['type'] ?? null,
        );
    }
}
