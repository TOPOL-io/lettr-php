<?php

declare(strict_types=1);

namespace Lettr\Dto;

/**
 * API rate limit information extracted from response headers.
 *
 * Present on all API responses (3 requests per second per team).
 */
final readonly class RateLimit
{
    public function __construct(
        public int $limit,
        public int $remaining,
        public int $reset,
    ) {}

    /**
     * Create from response headers array.
     *
     * @param  array<string, string|string[]>  $headers
     */
    public static function fromHeaders(array $headers): ?self
    {
        $limit = self::headerValue($headers, 'X-RateLimit-Limit');

        if ($limit === null) {
            return null;
        }

        return new self(
            limit: $limit,
            remaining: self::headerValue($headers, 'X-RateLimit-Remaining') ?? 0,
            reset: self::headerValue($headers, 'X-RateLimit-Reset') ?? 0,
        );
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
