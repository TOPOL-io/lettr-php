<?php

declare(strict_types=1);

namespace Lettr\Services;

use Lettr\Collections\WebhookCollection;
use Lettr\Contracts\TransporterContract;
use Lettr\Dto\Webhook\Webhook;
use Lettr\ValueObjects\WebhookId;

/**
 * Service for managing webhooks via the Lettr API.
 */
final class WebhookService
{
    private const WEBHOOKS_ENDPOINT = 'webhooks';

    public function __construct(
        private readonly TransporterContract $transporter,
    ) {}

    /**
     * List all webhooks.
     */
    public function list(): WebhookCollection
    {
        /**
         * @var array{
         *     webhooks: array<int, array{
         *         id: string,
         *         name: string,
         *         url: string,
         *         enabled: bool,
         *         auth_type: string,
         *         event_types: array<string>,
         *         last_status?: string|null,
         *         last_triggered_at?: string|null,
         *         last_error?: string|null,
         *         created_at: string,
         *         updated_at?: string|null,
         *     }>
         * } $response
         */
        $response = $this->transporter->get(self::WEBHOOKS_ENDPOINT);

        $webhooks = array_map(
            static fn (array $webhook): Webhook => Webhook::from($webhook),
            $response['webhooks']
        );

        return WebhookCollection::from($webhooks);
    }

    /**
     * Get webhook details.
     */
    public function get(string|WebhookId $webhookId): Webhook
    {
        $id = $webhookId instanceof WebhookId ? (string) $webhookId : $webhookId;

        /**
         * @var array{
         *     id: string,
         *     name: string,
         *     url: string,
         *     enabled: bool,
         *     auth_type: string,
         *     event_types: array<string>,
         *     last_status?: string|null,
         *     last_triggered_at?: string|null,
         *     last_error?: string|null,
         *     created_at: string,
         *     updated_at?: string|null,
         * } $response
         */
        $response = $this->transporter->get(self::WEBHOOKS_ENDPOINT.'/'.$id);

        return Webhook::from($response);
    }
}
