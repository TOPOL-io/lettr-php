<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Builders\EmailBuilder;
use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Email\ListEmailsFilter;
use Lettr\Dto\Email\SendEmailData;
use Lettr\Dto\Email\SendEmailResponse;
use Lettr\Responses\GetEmailResponse;
use Lettr\Responses\ListEmailsResponse;
use Lettr\ValueObjects\RequestId;

/**
 * Service for sending and managing emails via the Lettr API.
 */
final class EmailService
{
    private const EMAILS_ENDPOINT = 'emails';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * Create a new email builder for fluent email construction.
     */
    public function create(): EmailBuilder
    {
        return EmailBuilder::create();
    }

    /**
     * Send an email.
     */
    public function send(SendEmailData|EmailBuilder $data): SendEmailResponse
    {
        $emailData = $data instanceof EmailBuilder ? $data->build() : $data;

        /** @var array{request_id: string, accepted: int, rejected: int} $response */
        $response = $this->transporter->post(self::EMAILS_ENDPOINT, $emailData->toArray());

        return SendEmailResponse::from($response);
    }

    /**
     * Send an HTML email.
     *
     * @param  string|array{email: string, name?: string}  $from
     * @param  array<string>|string  $to
     * @param  array<string, mixed>|null  $substitutionData
     */
    public function sendHtml(
        string|array $from,
        array|string $to,
        string $subject,
        string $html,
        ?array $substitutionData = null,
    ): SendEmailResponse {
        $builder = $this->create()
            ->to(is_array($to) ? $to : [$to])
            ->subject($subject)
            ->html($html);

        if (is_array($from)) {
            $builder->from($from['email'], $from['name'] ?? null);
        } else {
            $builder->from($from);
        }

        if ($substitutionData !== null) {
            $builder->substitutionData($substitutionData);
        }

        return $this->send($builder);
    }

    /**
     * Send a plain text email.
     *
     * @param  string|array{email: string, name?: string}  $from
     * @param  array<string>|string  $to
     * @param  array<string, mixed>|null  $substitutionData
     */
    public function sendText(
        string|array $from,
        array|string $to,
        string $subject,
        string $text,
        ?array $substitutionData = null,
    ): SendEmailResponse {
        $builder = $this->create()
            ->to(is_array($to) ? $to : [$to])
            ->subject($subject)
            ->text($text);

        if (is_array($from)) {
            $builder->from($from['email'], $from['name'] ?? null);
        } else {
            $builder->from($from);
        }

        if ($substitutionData !== null) {
            $builder->substitutionData($substitutionData);
        }

        return $this->send($builder);
    }

    /**
     * Send an email using a template.
     *
     * @param  string|array{email: string, name?: string}  $from
     * @param  array<string>|string  $to
     * @param  array<string, mixed>|null  $substitutionData
     */
    public function sendTemplate(
        string|array $from,
        array|string $to,
        string $subject,
        string $templateSlug,
        ?int $templateVersion = null,
        ?int $projectId = null,
        ?array $substitutionData = null,
    ): SendEmailResponse {
        $builder = $this->create()
            ->to(is_array($to) ? $to : [$to])
            ->subject($subject)
            ->useTemplate($templateSlug, $templateVersion, $projectId);

        if (is_array($from)) {
            $builder->from($from['email'], $from['name'] ?? null);
        } else {
            $builder->from($from);
        }

        if ($substitutionData !== null) {
            $builder->substitutionData($substitutionData);
        }

        return $this->send($builder);
    }

    /**
     * List email events with optional filtering.
     */
    public function list(?ListEmailsFilter $filter = null): ListEmailsResponse
    {
        $query = $filter?->toArray() ?? [];

        /**
         * @var array{
         *     events: array<int, array{
         *         request_id: string,
         *         message_id: string,
         *         type: string,
         *         timestamp: string,
         *         recipient: string,
         *         from: string,
         *         subject: string,
         *         campaign_id?: string|null,
         *         ip_address?: string|null,
         *         user_agent?: string|null,
         *         click_url?: string|null,
         *         bounce_class?: string|null,
         *         reason?: string|null,
         *         error_code?: string|null,
         *     }>,
         *     total_count: int,
         *     pagination: array{next_cursor?: string|null, per_page: int},
         * } $response
         */
        $response = $this->transporter->getWithQuery(self::EMAILS_ENDPOINT, $query);

        return ListEmailsResponse::from($response);
    }

    /**
     * Get email events by request ID.
     */
    public function get(string|RequestId $requestId): GetEmailResponse
    {
        $id = $requestId instanceof RequestId ? (string) $requestId : $requestId;

        /**
         * @var array{
         *     events: array<int, array{
         *         request_id: string,
         *         message_id: string,
         *         type: string,
         *         timestamp: string,
         *         recipient: string,
         *         from: string,
         *         subject: string,
         *         campaign_id?: string|null,
         *         ip_address?: string|null,
         *         user_agent?: string|null,
         *         click_url?: string|null,
         *         bounce_class?: string|null,
         *         reason?: string|null,
         *         error_code?: string|null,
         *     }>,
         *     total_count: int,
         * } $response
         */
        $response = $this->transporter->get(self::EMAILS_ENDPOINT.'/'.$id);

        return GetEmailResponse::from($response);
    }
}
