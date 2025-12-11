<?php

declare(strict_types=1);

use Lettr\Dto\SendEmailData;

test('can create SendEmailData with required fields', function (): void {
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
    );

    expect($data->from)->toBe('sender@example.com')
        ->and($data->to)->toBe(['recipient@example.com'])
        ->and($data->subject)->toBe('Test Subject')
        ->and($data->text)->toBeNull()
        ->and($data->html)->toBeNull();
});

test('can create SendEmailData with all fields', function (): void {
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
        text: 'Plain text body',
        html: '<p>HTML body</p>',
    );

    expect($data->from)->toBe('sender@example.com')
        ->and($data->to)->toBe(['recipient@example.com'])
        ->and($data->subject)->toBe('Test Subject')
        ->and($data->text)->toBe('Plain text body')
        ->and($data->html)->toBe('<p>HTML body</p>');
});

test('can create SendEmailData from array', function (): void {
    $data = SendEmailData::from([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
        'html' => '<p>HTML body</p>',
    ]);

    expect($data->from)->toBe('sender@example.com')
        ->and($data->to)->toBe(['recipient@example.com'])
        ->and($data->subject)->toBe('Test Subject')
        ->and($data->text)->toBe('Plain text body')
        ->and($data->html)->toBe('<p>HTML body</p>');
});

test('toArray returns correct structure with required fields only', function (): void {
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
    );

    $array = $data->toArray();

    expect($array)->toBe([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
    ]);
});

test('toArray returns correct structure with all fields', function (): void {
    $data = new SendEmailData(
        from: 'sender@example.com',
        to: ['recipient@example.com'],
        subject: 'Test Subject',
        text: 'Plain text body',
        html: '<p>HTML body</p>',
    );

    $array = $data->toArray();

    expect($array)->toBe([
        'from' => 'sender@example.com',
        'to' => ['recipient@example.com'],
        'subject' => 'Test Subject',
        'text' => 'Plain text body',
        'html' => '<p>HTML body</p>',
    ]);
});
