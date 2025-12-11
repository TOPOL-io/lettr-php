<?php

declare(strict_types=1);

use Lettr\Lettr;
use Lettr\Services\EmailService;

test('can create Lettr instance with client method', function (): void {
    $lettr = Lettr::client('test-api-key');

    expect($lettr)->toBeInstanceOf(Lettr::class);
});

test('can create Lettr instance with custom base url', function (): void {
    $lettr = Lettr::client('test-api-key', 'https://custom.api.com');

    expect($lettr)->toBeInstanceOf(Lettr::class);
});

test('can access emails service via method', function (): void {
    $lettr = Lettr::client('test-api-key');

    expect($lettr->emails())->toBeInstanceOf(EmailService::class);
});

test('can access emails service via property', function (): void {
    $lettr = Lettr::client('test-api-key');

    expect($lettr->emails)->toBeInstanceOf(EmailService::class);
});

test('emails service is cached', function (): void {
    $lettr = Lettr::client('test-api-key');

    $emails1 = $lettr->emails();
    $emails2 = $lettr->emails();

    expect($emails1)->toBe($emails2);
});

test('throws exception for unknown service', function (): void {
    $lettr = Lettr::client('test-api-key');

    $lettr->unknownService;
})->throws(InvalidArgumentException::class, 'Unknown service: unknownService');

test('has correct version constant', function (): void {
    expect(Lettr::VERSION)->toBe('1.0.0');
});

test('has correct default base url constant', function (): void {
    expect(Lettr::DEFAULT_BASE_URL)->toBe('https://app.uselettr.com');
});
