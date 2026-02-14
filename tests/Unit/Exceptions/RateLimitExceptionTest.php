<?php

declare(strict_types=1);

use Lettr\Dto\RateLimit;
use Lettr\Exceptions\ApiException;
use Lettr\Exceptions\RateLimitException;

test('has 429 status code', function (): void {
    $exception = new RateLimitException;

    expect($exception->getCode())->toBe(429);
});

test('has default message', function (): void {
    $exception = new RateLimitException;

    expect($exception->getMessage())->toBe('Rate limit exceeded. Please slow down your requests.');
});

test('extends ApiException', function (): void {
    $exception = new RateLimitException;

    expect($exception)->toBeInstanceOf(ApiException::class);
});

test('includes rate limit information', function (): void {
    $rateLimit = RateLimit::fromHeaders([
        'X-RateLimit-Limit' => '3',
        'X-RateLimit-Remaining' => '0',
        'X-RateLimit-Reset' => '1740787201',
    ]);

    $exception = new RateLimitException('Rate limit exceeded.', $rateLimit, 1);

    expect($exception->rateLimit)->toBeInstanceOf(RateLimit::class)
        ->and($exception->rateLimit->limit)->toBe(3)
        ->and($exception->rateLimit->remaining)->toBe(0)
        ->and($exception->retryAfter)->toBe(1);
});

test('rate limit and retry after are null when not provided', function (): void {
    $exception = new RateLimitException;

    expect($exception->rateLimit)->toBeNull()
        ->and($exception->retryAfter)->toBeNull();
});
