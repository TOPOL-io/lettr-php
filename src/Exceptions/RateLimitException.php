<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

use Lettr\Dto\RateLimit;

/**
 * Exception thrown when the API rate limit is exceeded (HTTP 429).
 */
final class RateLimitException extends ApiException
{
    public readonly ?RateLimit $rateLimit;

    public readonly ?int $retryAfter;

    public function __construct(
        string $message = 'Rate limit exceeded. Please slow down your requests.',
        ?RateLimit $rateLimit = null,
        ?int $retryAfter = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 429, $previous);
        $this->rateLimit = $rateLimit;
        $this->retryAfter = $retryAfter;
    }
}
