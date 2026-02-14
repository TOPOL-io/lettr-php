<?php

declare(strict_types=1);

use Lettr\Dto\RateLimit;

test('creates from response headers', function (): void {
    $headers = [
        'X-RateLimit-Limit' => '3',
        'X-RateLimit-Remaining' => '2',
        'X-RateLimit-Reset' => '1740787201',
    ];

    $rateLimit = RateLimit::fromHeaders($headers);

    expect($rateLimit)->toBeInstanceOf(RateLimit::class)
        ->and($rateLimit->limit)->toBe(3)
        ->and($rateLimit->remaining)->toBe(2)
        ->and($rateLimit->reset)->toBe(1740787201);
});

test('returns null when no rate limit headers present', function (): void {
    $rateLimit = RateLimit::fromHeaders([]);

    expect($rateLimit)->toBeNull();
});

test('returns null when only unrelated headers present', function (): void {
    $rateLimit = RateLimit::fromHeaders([
        'Content-Type' => 'application/json',
    ]);

    expect($rateLimit)->toBeNull();
});

test('handles array header values', function (): void {
    $headers = [
        'X-RateLimit-Limit' => ['3'],
        'X-RateLimit-Remaining' => ['0'],
        'X-RateLimit-Reset' => ['1740787201'],
    ];

    $rateLimit = RateLimit::fromHeaders($headers);

    expect($rateLimit)->not->toBeNull()
        ->and($rateLimit->limit)->toBe(3)
        ->and($rateLimit->remaining)->toBe(0);
});
