<?php

declare(strict_types=1);

namespace Lettr\Dto;

use Lettr\ValueObjects\Timestamp;

/**
 * Health check response.
 */
final readonly class HealthStatus
{
    public function __construct(
        public string $status,
        public Timestamp $timestamp,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{status: string, timestamp: string}  $data
     */
    public static function from(array $data): self
    {
        return new self(
            status: $data['status'],
            timestamp: Timestamp::fromString($data['timestamp']),
        );
    }

    /**
     * Check if the API is healthy.
     */
    public function isHealthy(): bool
    {
        return $this->status === 'ok';
    }
}
