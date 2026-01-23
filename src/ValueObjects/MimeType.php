<?php

declare(strict_types=1);

namespace Lettr\ValueObjects;

use Lettr\Exceptions\InvalidValueException;
use Stringable;

/**
 * Value object representing a MIME type.
 */
final readonly class MimeType implements Stringable
{
    /**
     * Common MIME types for email attachments.
     */
    public const APPLICATION_PDF = 'application/pdf';

    public const APPLICATION_JSON = 'application/json';

    public const APPLICATION_XML = 'application/xml';

    public const APPLICATION_ZIP = 'application/zip';

    public const APPLICATION_OCTET_STREAM = 'application/octet-stream';

    public const IMAGE_PNG = 'image/png';

    public const IMAGE_JPEG = 'image/jpeg';

    public const IMAGE_GIF = 'image/gif';

    public const IMAGE_WEBP = 'image/webp';

    public const TEXT_PLAIN = 'text/plain';

    public const TEXT_HTML = 'text/html';

    public const TEXT_CSV = 'text/csv';

    public string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));

        if ($value === '') {
            throw new InvalidValueException('MIME type cannot be empty.');
        }

        // Basic validation: should contain a slash
        if (! str_contains($value, '/')) {
            throw new InvalidValueException(
                sprintf('Invalid MIME type format: %s', $value)
            );
        }

        $this->value = $value;
    }

    public static function from(string|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return new self($value);
    }

    /**
     * Get the main type (e.g., "image" from "image/png").
     */
    public function mainType(): string
    {
        return explode('/', $this->value, 2)[0];
    }

    /**
     * Get the subtype (e.g., "png" from "image/png").
     */
    public function subType(): string
    {
        $parts = explode('/', $this->value, 2);

        return $parts[1] ?? '';
    }

    public function isImage(): bool
    {
        return $this->mainType() === 'image';
    }

    public function isText(): bool
    {
        return $this->mainType() === 'text';
    }

    public function isApplication(): bool
    {
        return $this->mainType() === 'application';
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
