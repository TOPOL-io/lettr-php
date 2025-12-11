<?php

declare(strict_types=1);

use Lettr\Dto\EmailResponse;

test('can create EmailResponse', function (): void {
    $response = new EmailResponse(id: 'email_123');

    expect($response->id)->toBe('email_123');
});

test('can create EmailResponse from array', function (): void {
    $response = EmailResponse::from([
        'id' => 'email_123',
    ]);

    expect($response->id)->toBe('email_123');
});

test('toArray returns correct structure', function (): void {
    $response = new EmailResponse(id: 'email_123');

    $array = $response->toArray();

    expect($array)->toBe(['id' => 'email_123']);
});
