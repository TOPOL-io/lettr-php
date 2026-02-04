<?php

declare(strict_types=1);

use Lettr\Contracts\TransporterContract;
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
