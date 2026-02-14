<?php

declare(strict_types=1);

use Lettr\Dto\SendingQuota;

test('creates from response headers', function (): void {
    $headers = [
        'X-Monthly-Limit' => '3000',
        'X-Monthly-Remaining' => '2500',
        'X-Monthly-Reset' => '1740787200',
        'X-Daily-Limit' => '100',
        'X-Daily-Remaining' => '75',
        'X-Daily-Reset' => '1739600000',
    ];

    $quota = SendingQuota::fromHeaders($headers);

    expect($quota)->toBeInstanceOf(SendingQuota::class)
        ->and($quota->monthlyLimit)->toBe(3000)
        ->and($quota->monthlyRemaining)->toBe(2500)
        ->and($quota->monthlyReset)->toBe(1740787200)
        ->and($quota->dailyLimit)->toBe(100)
        ->and($quota->dailyRemaining)->toBe(75)
        ->and($quota->dailyReset)->toBe(1739600000);
});

test('returns null when no quota headers present', function (): void {
    $quota = SendingQuota::fromHeaders([]);

    expect($quota)->toBeNull();
});

test('returns null when only unrelated headers present', function (): void {
    $quota = SendingQuota::fromHeaders([
        'Content-Type' => 'application/json',
        'X-Request-Id' => 'abc123',
    ]);

    expect($quota)->toBeNull();
});

test('handles array header values', function (): void {
    $headers = [
        'X-Monthly-Limit' => ['3000'],
        'X-Monthly-Remaining' => ['0'],
        'X-Monthly-Reset' => ['1740787200'],
        'X-Daily-Limit' => ['100'],
        'X-Daily-Remaining' => ['50'],
        'X-Daily-Reset' => ['1739600000'],
    ];

    $quota = SendingQuota::fromHeaders($headers);

    expect($quota)->not->toBeNull()
        ->and($quota->monthlyLimit)->toBe(3000)
        ->and($quota->monthlyRemaining)->toBe(0);
});

test('isMonthlyQuotaExhausted returns true when remaining is zero', function (): void {
    $quota = SendingQuota::fromHeaders([
        'X-Monthly-Limit' => '3000',
        'X-Monthly-Remaining' => '0',
        'X-Monthly-Reset' => '1740787200',
        'X-Daily-Limit' => '100',
        'X-Daily-Remaining' => '50',
        'X-Daily-Reset' => '1739600000',
    ]);

    expect($quota->isMonthlyQuotaExhausted())->toBeTrue()
        ->and($quota->isDailyQuotaExhausted())->toBeFalse();
});

test('isDailyQuotaExhausted returns true when remaining is zero', function (): void {
    $quota = SendingQuota::fromHeaders([
        'X-Monthly-Limit' => '3000',
        'X-Monthly-Remaining' => '2500',
        'X-Monthly-Reset' => '1740787200',
        'X-Daily-Limit' => '100',
        'X-Daily-Remaining' => '0',
        'X-Daily-Reset' => '1739600000',
    ]);

    expect($quota->isDailyQuotaExhausted())->toBeTrue()
        ->and($quota->isMonthlyQuotaExhausted())->toBeFalse();
});
