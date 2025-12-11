<?php

declare(strict_types=1);

namespace Lettr\Dto;

/**
 * Data Transfer Object for email API response.
 */
final readonly class EmailResponse
{
    /**
     * @param  string  $id  The unique identifier for the email
     */
    public function __construct(
        public string $id,
    ) {}

    /**
     * Create a new instance from an API response array.
     *
     * @param  array{
     *     id: string,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }

    /**
     * Convert the response to an array.
     *
     * @return array{id: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
