<?php

declare(strict_types=1);

use Lettr\Dto\SendingQuota;
use Lettr\Exceptions\ApiException;
use Lettr\Exceptions\QuotaExceededException;

test('has 429 status code', function (): void {
    $exception = new QuotaExceededException;

    expect($exception->getCode())->toBe(429);
});

test('has default message', function (): void {
    $exception = new QuotaExceededException;

    expect($exception->getMessage())->toBe('Sending quota exceeded.');
});

test('accepts custom message', function (): void {
    $exception = new QuotaExceededException('Daily sending quota exceeded. Please try again tomorrow.');

    expect($exception->getMessage())->toBe('Daily sending quota exceeded. Please try again tomorrow.');
});

test('extends ApiException', function (): void {
    $exception = new QuotaExceededException;

    expect($exception)->toBeInstanceOf(ApiException::class);
});

test('includes quota information', function (): void {
    $quota = SendingQuota::fromHeaders([
        'X-Monthly-Limit' => '3000',
        'X-Monthly-Remaining' => '0',
        'X-Monthly-Reset' => '1740787200',
        'X-Daily-Limit' => '100',
        'X-Daily-Remaining' => '0',
        'X-Daily-Reset' => '1739600000',
    ]);

    $exception = new QuotaExceededException('Sending quota exceeded.', $quota);

    expect($exception->quota)->toBeInstanceOf(SendingQuota::class)
        ->and($exception->quota->monthlyLimit)->toBe(3000)
        ->and($exception->quota->monthlyRemaining)->toBe(0);
});

test('quota is null when not provided', function (): void {
    $exception = new QuotaExceededException;

    expect($exception->quota)->toBeNull();
});
