<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Contracts\Arrayable;
use Lettr\ValueObjects\Base64Data;
use Lettr\ValueObjects\MimeType;

/**
 * Email attachment.
 */
final readonly class Attachment implements Arrayable
{
    public function __construct(
        public string $name,
        public MimeType $type,
        public Base64Data $data,
    ) {}

    /**
     * Create from an array.
     *
     * @param  array{
     *     name: string,
     *     type: string,
     *     data: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            type: MimeType::from($data['type']),
            data: Base64Data::from($data['data']),
        );
    }

    /**
     * Create from a file path.
     */
    public static function fromFile(string $path, ?string $name = null, ?string $mimeType = null): self
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }

        return new self(
            name: $name ?? basename($path),
            type: MimeType::from($mimeType ?? mime_content_type($path) ?: 'application/octet-stream'),
            data: Base64Data::fromBinary($contents),
        );
    }

    /**
     * Create from binary data.
     */
    public static function fromBinary(string $data, string $name, string $mimeType): self
    {
        return new self(
            name: $name,
            type: MimeType::from($mimeType),
            data: Base64Data::fromBinary($data),
        );
    }

    /**
     * @return array{name: string, type: string, data: string}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => (string) $this->type,
            'data' => (string) $this->data,
        ];
    }
}
