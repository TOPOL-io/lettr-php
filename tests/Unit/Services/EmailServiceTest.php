<?php

declare(strict_types=1);

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\EmailResponse;
use Lettr\Dto\SendEmailData;
use Lettr\Services\EmailService;

/**
 * Simple mock transporter for testing.
 */
class MockTransporter implements TransporterContract
{
    public ?string $lastUri = null;

    /** @var array<string, mixed>|null */
    public ?array $lastData = null;

    /** @var array<string, mixed> */
    public array $response = [];

    public function post(string $uri, array $data): array
    {
        $this->lastUri = $uri;
        $this->lastData = $data;

        return $this->response;
    }

    public function get(string $uri): array
    {
        $this->lastUri = $uri;

        return $this->response;
    }
}

test('can create EmailService instance', function (): void {
    $transporter = new MockTransporter;
    $service = new EmailService($transporter);

    expect($service)->toBeInstanceOf(EmailService::class);
});

test('send method calls transporter with correct data', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['id' => 'email_123'];

    $service = new EmailService($transporter);
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
        text: 'Plain text body',
    );

    $response = $service->send($data);

    expect($transporter->lastUri)->toBe('/api/emails')
        ->and($transporter->lastData)->toBe([
            'from' => 'sender@example.com',
            'to' => ['recipient@example.com'],
            'subject' => 'Test Subject',
            'text' => 'Plain text body',
        ])
        ->and($response)->toBeInstanceOf(EmailResponse::class)
        ->and($response->id)->toBe('email_123');
});

test('send method with html content', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['id' => 'email_456'];

    $service = new EmailService($transporter);
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
        html: '<p>HTML body</p>',
    );

    $response = $service->send($data);

    expect($transporter->lastUri)->toBe('/api/emails')
        ->and($transporter->lastData)->toBe([
            'from' => 'sender@example.com',
            'to' => ['recipient@example.com'],
            'subject' => 'Test Subject',
            'html' => '<p>HTML body</p>',
        ])
        ->and($response)->toBeInstanceOf(EmailResponse::class)
        ->and($response->id)->toBe('email_456');
});
