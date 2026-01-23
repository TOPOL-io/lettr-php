<?php

declare(strict_types=1);

use Lettr\Builders\EmailBuilder;
use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Email\SendEmailData;
use Lettr\Dto\Email\SendEmailResponse;
use Lettr\Responses\GetEmailResponse;
use Lettr\Responses\ListEmailsResponse;
use Lettr\Services\EmailService;

/**
 * Simple mock transporter for testing.
 */
class MockTransporter implements TransporterContract
{
    public ?string $lastUri = null;

    /** @var array<string, mixed>|null */
    public ?array $lastData = null;

    /** @var array<string, mixed>|null */
    public ?array $lastQuery = null;

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

    public function getWithQuery(string $uri, array $query = []): array
    {
        $this->lastUri = $uri;
        $this->lastQuery = $query;

        return $this->response;
    }

    public function delete(string $uri): void
    {
        $this->lastUri = $uri;
    }
}

test('can create EmailService instance', function (): void {
    $transporter = new MockTransporter;
    $service = new EmailService($transporter);

    expect($service)->toBeInstanceOf(EmailService::class);
});

test('create returns EmailBuilder', function (): void {
    $transporter = new MockTransporter;
    $service = new EmailService($transporter);

    expect($service->create())->toBeInstanceOf(EmailBuilder::class);
});

test('send method calls transporter with correct data', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_123', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $data = SendEmailData::from([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
    ]);

    $response = $service->send($data);

    expect($transporter->lastUri)->toBe('emails')
        ->and($transporter->lastData['from'])->toBe('sender@example.com')
        ->and($transporter->lastData['to'])->toBe(['recipient@example.com'])
        ->and($transporter->lastData['subject'])->toBe('Test Subject')
        ->and($transporter->lastData['text'])->toBe('Plain text body')
        ->and($response)->toBeInstanceOf(SendEmailResponse::class)
        ->and((string) $response->requestId)->toBe('req_123')
        ->and($response->accepted)->toBe(1)
        ->and($response->rejected)->toBe(0);
});

test('send method works with EmailBuilder', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_456', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $builder = $service->create()
        ->from('sender@example.com')
        ->to(['recipient@example.com'])
        ->subject('Test Subject')
        ->html('<p>HTML body</p>');

    $response = $service->send($builder);

    expect($transporter->lastUri)->toBe('emails')
        ->and($response)->toBeInstanceOf(SendEmailResponse::class)
        ->and((string) $response->requestId)->toBe('req_456');
});

test('list method returns ListEmailsResponse', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = [
        'events' => [
            [
                'request_id' => 'req_123',
                'message_id' => 'msg_123',
                'type' => 'delivery',
                'timestamp' => '2024-01-01T12:00:00+00:00',
                'recipient' => 'test@example.com',
                'from' => 'sender@example.com',
                'subject' => 'Test',
            ],
        ],
        'total_count' => 1,
        'pagination' => ['per_page' => 10],
    ];

    $service = new EmailService($transporter);
    $response = $service->list();

    expect($transporter->lastUri)->toBe('emails')
        ->and($response)->toBeInstanceOf(ListEmailsResponse::class)
        ->and($response->totalCount)->toBe(1);
});

test('get method returns GetEmailResponse', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = [
        'events' => [
            [
                'request_id' => 'req_123',
                'message_id' => 'msg_123',
                'type' => 'delivery',
                'timestamp' => '2024-01-01T12:00:00+00:00',
                'recipient' => 'test@example.com',
                'from' => 'sender@example.com',
                'subject' => 'Test',
            ],
        ],
        'total_count' => 1,
    ];

    $service = new EmailService($transporter);
    $response = $service->get('req_123');

    expect($transporter->lastUri)->toBe('emails/req_123')
        ->and($response)->toBeInstanceOf(GetEmailResponse::class)
        ->and($response->totalCount)->toBe(1);
});

test('sendHtml helper sends HTML email', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_html', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $response = $service->sendHtml(
        from: 'sender@example.com',
        to: 'recipient@example.com',
        subject: 'HTML Test',
        html: '<h1>Hello</h1>',
    );

    expect($transporter->lastData['from'])->toBe('sender@example.com')
        ->and($transporter->lastData['to'])->toBe(['recipient@example.com'])
        ->and($transporter->lastData['subject'])->toBe('HTML Test')
        ->and($transporter->lastData['html'])->toBe('<h1>Hello</h1>')
        ->and($transporter->lastData)->not->toHaveKey('text')
        ->and((string) $response->requestId)->toBe('req_html');
});

test('sendHtml helper with from name', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_html2', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $service->sendHtml(
        from: ['email' => 'sender@example.com', 'name' => 'Sender Name'],
        to: ['recipient@example.com'],
        subject: 'HTML Test',
        html: '<h1>Hello</h1>',
    );

    expect($transporter->lastData['from'])->toBe(['email' => 'sender@example.com', 'name' => 'Sender Name']);
});

test('sendText helper sends plain text email', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_text', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $response = $service->sendText(
        from: 'sender@example.com',
        to: 'recipient@example.com',
        subject: 'Text Test',
        text: 'Hello World',
    );

    expect($transporter->lastData['from'])->toBe('sender@example.com')
        ->and($transporter->lastData['to'])->toBe(['recipient@example.com'])
        ->and($transporter->lastData['subject'])->toBe('Text Test')
        ->and($transporter->lastData['text'])->toBe('Hello World')
        ->and($transporter->lastData)->not->toHaveKey('html')
        ->and((string) $response->requestId)->toBe('req_text');
});

test('sendTemplate helper sends template email', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_tpl', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $response = $service->sendTemplate(
        from: 'sender@example.com',
        to: 'recipient@example.com',
        subject: 'Template Test',
        templateSlug: 'welcome-email',
        templateVersion: 2,
        projectId: 123,
        substitutionData: ['name' => 'John'],
    );

    expect($transporter->lastData['from'])->toBe('sender@example.com')
        ->and($transporter->lastData['to'])->toBe(['recipient@example.com'])
        ->and($transporter->lastData['subject'])->toBe('Template Test')
        ->and($transporter->lastData['template_slug'])->toBe('welcome-email')
        ->and($transporter->lastData['template_version'])->toBe(2)
        ->and($transporter->lastData['project_id'])->toBe(123)
        ->and($transporter->lastData['substitution_data'])->toBe(['name' => 'John'])
        ->and((string) $response->requestId)->toBe('req_tpl');
});

test('sendTemplate helper without optional parameters', function (): void {
    $transporter = new MockTransporter;
    $transporter->response = ['request_id' => 'req_tpl2', 'accepted' => 1, 'rejected' => 0];

    $service = new EmailService($transporter);
    $service->sendTemplate(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Template Test',
        templateSlug: 'simple-template',
    );

    expect($transporter->lastData['template_slug'])->toBe('simple-template')
        ->and($transporter->lastData)->not->toHaveKey('template_version')
        ->and($transporter->lastData)->not->toHaveKey('project_id')
        ->and($transporter->lastData)->not->toHaveKey('substitution_data');
});
