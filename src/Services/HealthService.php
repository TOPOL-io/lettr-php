<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\AuthStatus;
use Lettr\Dto\HealthStatus;

/**
 * Service for checking API health and authentication.
 */
final class HealthService
{
    private const HEALTH_ENDPOINT = 'health';

    private const AUTH_CHECK_ENDPOINT = 'auth/check';

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

    /**
     * Check if the API key is valid and get team info.
     */
    public function authCheck(): AuthStatus
    {
        /** @var array{team_id: int, timestamp: string} $response */
        $response = $this->transporter->get(self::AUTH_CHECK_ENDPOINT);

        return AuthStatus::from($response);
    }
}
