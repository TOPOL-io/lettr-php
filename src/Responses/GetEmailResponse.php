<?php

declare(strict_types=1);

namespace Lettr\Responses;

use Lettr\Collections\EmailEventCollection;
use Lettr\Dto\Email\EmailEvent;

/**
 * Response from getting email events by request ID.
 */
final readonly class GetEmailResponse
{
    public function __construct(
        public EmailEventCollection $events,
        public int $totalCount,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
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
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            events: EmailEventCollection::from(
                array_map(
                    static fn (array $event): EmailEvent => EmailEvent::from($event),
                    $data['events']
                )
            ),
            totalCount: $data['total_count'],
        );
    }
}
