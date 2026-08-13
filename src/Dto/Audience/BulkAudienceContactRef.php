<?php

declare(strict_types=1);

namespace Lettr\Dto\Audience;

/**
 * Identity of a contact that exists after a bulk create, so the caller can
 * chain into the bulk list and topic endpoints without looking ids up again.
 */
final readonly class BulkAudienceContactRef
{
    /**
     * @param  bool  $created  `true` when this request created the contact,
     *                         `false` when it already existed.
     */
    public function __construct(
        public string $id,
        public string $email,
        public bool $created,
    ) {}

    /**
     * @param  array{id: string, email: string, created: bool}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            created: $data['created'],
        );
    }
}
