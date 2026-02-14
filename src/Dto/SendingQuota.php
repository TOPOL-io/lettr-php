<?php

declare(strict_types=1);

namespace Lettr\Dto;

/**
 * Sending quota information extracted from API response headers.
 *
 * Present for free tier teams on send email responses (both 200 and 429).
 */
final readonly class SendingQuota
{
    public function __construct(
        public int $monthlyLimit,
        public int $monthlyRemaining,
        public int $monthlyReset,
        public int $dailyLimit,
        public int $dailyRemaining,
        public int $dailyReset,
    ) {}

    /**
     * Create from response headers array.
     *
     * @param  array<string, string|string[]>  $headers
     */
    public static function fromHeaders(array $headers): ?self
    {
        $monthlyLimit = self::headerValue($headers, 'X-Monthly-Limit');
        $dailyLimit = self::headerValue($headers, 'X-Daily-Limit');

        if ($monthlyLimit === null && $dailyLimit === null) {
            return null;
        }

        return new self(
            monthlyLimit: $monthlyLimit ?? 0,
            monthlyRemaining: self::headerValue($headers, 'X-Monthly-Remaining') ?? 0,
            monthlyReset: self::headerValue($headers, 'X-Monthly-Reset') ?? 0,
            dailyLimit: $dailyLimit ?? 0,
            dailyRemaining: self::headerValue($headers, 'X-Daily-Remaining') ?? 0,
            dailyReset: self::headerValue($headers, 'X-Daily-Reset') ?? 0,
        );
    }

    /**
     * Check if the monthly quota is exhausted.
     */
    public function isMonthlyQuotaExhausted(): bool
    {
        return $this->monthlyRemaining <= 0;
    }

    /**
     * Check if the daily quota is exhausted.
     */
    public function isDailyQuotaExhausted(): bool
    {
        return $this->dailyRemaining <= 0;
    }

    /**
     * @param  array<string, string|string[]>  $headers
     */
    private static function headerValue(array $headers, string $name): ?int
    {
        $lower = strtolower($name);

        foreach ($headers as $key => $value) {
            if (strtolower($key) === $lower) {
                if (is_array($value)) {
                    $value = $value[0] ?? null;
                }

                return $value !== null ? (int) $value : null;
            }
        }

        return null;
    }
}
