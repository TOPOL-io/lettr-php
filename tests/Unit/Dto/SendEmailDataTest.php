<?php

declare(strict_types=1);

use Lettr\Collections\EmailAddressCollection;
use Lettr\Dto\Email\SendEmailData;
use Lettr\ValueObjects\EmailAddress;
use Lettr\ValueObjects\Subject;

test('can create SendEmailData with required fields', function (): void {
    $data = SendEmailData::from([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
    ]);

    expect($data->from)->toBeInstanceOf(EmailAddress::class)
        ->and($data->from->address)->toBe('sender@example.com')
        ->and($data->to)->toBeInstanceOf(EmailAddressCollection::class)
        ->and($data->to->toStrings())->toBe(['recipient@example.com'])
        ->and($data->subject)->toBeInstanceOf(Subject::class)
        ->and((string) $data->subject)->toBe('Test Subject')
        ->and($data->text)->toBe('Plain text body')
        ->and($data->html)->toBeNull();
});

test('can create SendEmailData with all fields', function (): void {
    $data = SendEmailData::from([
        'from' => ['email' => 'sender@example.com', 'name' => 'Sender Name'],
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
        'html' => '<p>HTML body</p>',
        'cc' => ['cc@example.com'],
        'bcc' => ['bcc@example.com'],
        'reply_to' => 'reply@example.com',
        'campaign_id' => 'campaign_123',
    ]);

    expect($data->from->address)->toBe('sender@example.com')
        ->and($data->from->name)->toBe('Sender Name')
        ->and($data->to->toStrings())->toBe(['recipient@example.com'])
        ->and((string) $data->subject)->toBe('Test Subject')
        ->and($data->text)->toBe('Plain text body')
        ->and($data->html)->toBe('<p>HTML body</p>')
        ->and($data->cc?->toStrings())->toBe(['cc@example.com'])
        ->and($data->bcc?->toStrings())->toBe(['bcc@example.com'])
        ->and($data->replyTo?->address)->toBe('reply@example.com')
        ->and((string) $data->campaignId)->toBe('campaign_123');
});

test('toArray returns correct structure with required fields only', function (): void {
    $data = SendEmailData::from([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
    ]);

    $array = $data->toArray();

    expect($array['from'])->toBe('sender@example.com')
        ->and($array['to'])->toBe(['recipient@example.com'])
        ->and($array['subject'])->toBe('Test Subject')
        ->and($array['text'])->toBe('Plain text body');
});

test('toArray includes from_name when provided', function (): void {
    $data = SendEmailData::from([
        'from' => ['email' => 'sender@example.com', 'name' => 'Sender Name'],
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
    ]);

    $array = $data->toArray();

    expect($array['from'])->toBe('sender@example.com')
        ->and($array['from_name'])->toBe('Sender Name');
});

test('toArray excludes null optional fields', function (): void {
    $data = SendEmailData::from([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'html' => '<p>HTML body</p>',
    ]);

    $array = $data->toArray();

    expect($array)->not->toHaveKey('text')
        ->and($array)->not->toHaveKey('cc')
        ->and($array)->not->toHaveKey('bcc')
        ->and($array)->not->toHaveKey('reply_to')
        ->and($array)->not->toHaveKey('attachments')
        ->and($array)->not->toHaveKey('metadata')
        ->and($array)->not->toHaveKey('substitution_data')
        ->and($array)->not->toHaveKey('campaign_id');
});
