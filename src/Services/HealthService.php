<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\HealthStatus;

/**
 * Service for checking API health.
 */
final class HealthService
{
    private const HEALTH_ENDPOINT = 'health';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * Check the API health status.
     */
    public function check(): HealthStatus
    {
        /** @var array{status: string, timestamp: string} $response */
        $response = $this->transporter->get(self::HEALTH_ENDPOINT);

        return HealthStatus::from($response);
    }
}
