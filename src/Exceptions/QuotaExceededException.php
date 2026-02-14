<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

use Lettr\Dto\SendingQuota;

/**
 * Exception thrown when the sending quota is exceeded (HTTP 429).
 */
final class QuotaExceededException extends ApiException
{
    public readonly ?SendingQuota $quota;

    public function __construct(
        string $message = 'Sending quota exceeded.',
        ?SendingQuota $quota = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 429, $previous);
        $this->quota = $quota;
    }
}
