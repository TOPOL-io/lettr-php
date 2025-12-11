<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Contracts\TransporterContract;
use Lettr\Dto\EmailResponse;
use Lettr\Dto\SendEmailData;

/**
 * Service for sending emails via the Lettr API.
 */
final class EmailService
{
    private const EMAILS_ENDPOINT = '/api/emails';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * Send an email using a DTO.
     */
    public function send(SendEmailData $data): EmailResponse
    {
        /** @var array{id: string, from?: string|null, to?: array<string>|null, subject?: string|null, created_at?: string|null} $response */
        $response = $this->transporter->post(self::EMAILS_ENDPOINT, $data->toArray());

        return EmailResponse::from($response);
    }
}
