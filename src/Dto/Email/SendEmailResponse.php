<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\ValueObjects\RequestId;

/**
 * Response from sending an email.
 */
final readonly class SendEmailResponse
{
    public function __construct(
        public RequestId $requestId,
        public int $accepted,
        public int $rejected,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     request_id: string,
     *     accepted: int,
     *     rejected: int,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            requestId: new RequestId($data['request_id']),
            accepted: $data['accepted'],
            rejected: $data['rejected'],
        );
    }

    /**
     * Check if all recipients were accepted.
     */
    public function allAccepted(): bool
    {
        return $this->rejected === 0;
    }

    /**
     * Check if any recipients were rejected.
     */
    public function hasRejections(): bool
    {
        return $this->rejected > 0;
    }

    /**
     * Get the total number of recipients.
     */
    public function total(): int
    {
        return $this->accepted + $this->rejected;
    }
}
