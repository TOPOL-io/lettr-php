<?php

declare(strict_types=1);

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\AuthStatus;
use Lettr\Dto\HealthStatus;
use Lettr\Services\HealthService;

/**
 * Simple mock transporter for testing.
 */
class HealthMockTransporter implements TransporterContract
{
    public ?string $lastUri = null;

    /** @var array<string, mixed> */
    public array $response = [];

    public function post(string $uri, array $data): array
    {
        return $this->response;
    }

    public function get(string $uri): array
    {
        $this->lastUri = $uri;

        return $this->response;
    }

    public function getWithQuery(string $uri, array $query = []): array
    {
        return $this->response;
    }

    public function delete(string $uri): void {}

    public function lastResponseHeaders(): array
    {
        return [];
    }
}

test('can create HealthService instance', function (): void {
    $transporter = new HealthMockTransporter;
    $service = new HealthService($transporter);

    expect($service)->toBeInstanceOf(HealthService::class);
});

test('check method returns HealthStatus', function (): void {
    $transporter = new HealthMockTransporter;
    $transporter->response = [
        'status' => 'ok',
        'timestamp' => '2024-01-20T12:00:00+00:00',
    ];

    $service = new HealthService($transporter);
    $status = $service->check();

    expect($transporter->lastUri)->toBe('health')
        ->and($status)->toBeInstanceOf(HealthStatus::class)
        ->and($status->status)->toBe('ok')
        ->and($status->isHealthy())->toBeTrue();
});

test('isHealthy returns false for non-ok status', function (): void {
    $status = HealthStatus::from([
        'status' => 'error',
        'timestamp' => '2024-01-20T12:00:00+00:00',
    ]);

    expect($status->isHealthy())->toBeFalse();
});

test('authCheck method returns AuthStatus', function (): void {
    $transporter = new HealthMockTransporter;
    $transporter->response = [
        'team_id' => 123,
        'timestamp' => '2024-01-20T12:00:00+00:00',
    ];

    $service = new HealthService($transporter);
    $status = $service->authCheck();

    expect($transporter->lastUri)->toBe('auth/check')
        ->and($status)->toBeInstanceOf(AuthStatus::class)
        ->and($status->teamId)->toBe(123);
});

test('AuthStatus from array', function (): void {
    $status = AuthStatus::from([
        'team_id' => 456,
        'timestamp' => '2024-01-20T15:30:00+00:00',
    ]);

    expect($status->teamId)->toBe(456)
        ->and($status->timestamp->toIso8601())->toBe('2024-01-20T15:30:00+00:00');
});
