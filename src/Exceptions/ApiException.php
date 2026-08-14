<?php

declare(strict_types=1);

namespace Lettr\Exceptions;

use Lettr\Enums\ErrorCode;

/**
 * Exception thrown when the API returns an error response.
 */
class ApiException extends LettrException
{
    /**
     * The machine-readable `error_code` from the response body, when the API
     * sent one. See {@see ErrorCode} for the known values; the raw
     * string is kept so a code added server-side is still readable here.
     */
    public readonly ?string $errorCode;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        ?string $errorCode = null,
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    /**
     * The machine-readable `error_code` from the response body, or `null` when
     * the API did not send one.
     */
    public function errorCode(): ?string
    {
        return $this->errorCode;
    }
}
