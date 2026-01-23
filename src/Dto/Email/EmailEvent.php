<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Enums\EventType;
use Lettr\ValueObjects\EmailAddress;
use Lettr\ValueObjects\IpAddress;
use Lettr\ValueObjects\MessageId;
use Lettr\ValueObjects\RequestId;
use Lettr\ValueObjects\Timestamp;

/**
 * Email event from the events list.
 */
final readonly class EmailEvent
{
    public function __construct(
        public RequestId $requestId,
        public MessageId $messageId,
        public EventType $type,
        public Timestamp $timestamp,
        public EmailAddress $recipient,
        public EmailAddress $from,
        public string $subject,
        public ?string $campaignId = null,
        public ?IpAddress $ipAddress = null,
        public ?string $userAgent = null,
        public ?string $clickUrl = null,
        public ?string $bounceClass = null,
        public ?string $reason = null,
        public ?string $errorCode = null,
    ) {}

    /**
     * Create from an API response array.
     *
     * @param  array{
     *     request_id: string,
     *     message_id: string,
     *     type: string,
     *     timestamp: string,
     *     recipient: string,
     *     from: string,
     *     subject: string,
     *     campaign_id?: string|null,
     *     ip_address?: string|null,
     *     user_agent?: string|null,
     *     click_url?: string|null,
     *     bounce_class?: string|null,
     *     reason?: string|null,
     *     error_code?: string|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            requestId: new RequestId($data['request_id']),
            messageId: new MessageId($data['message_id']),
            type: EventType::from($data['type']),
            timestamp: Timestamp::fromString($data['timestamp']),
            recipient: new EmailAddress($data['recipient']),
            from: new EmailAddress($data['from']),
            subject: $data['subject'],
            campaignId: $data['campaign_id'] ?? null,
            ipAddress: isset($data['ip_address']) ? new IpAddress($data['ip_address']) : null,
            userAgent: $data['user_agent'] ?? null,
            clickUrl: $data['click_url'] ?? null,
            bounceClass: $data['bounce_class'] ?? null,
            reason: $data['reason'] ?? null,
            errorCode: $data['error_code'] ?? null,
        );
    }

    /**
     * Check if this is a successful event.
     */
    public function isSuccess(): bool
    {
        return $this->type->isSuccess();
    }

    /**
     * Check if this is a failure event.
     */
    public function isFailure(): bool
    {
        return $this->type->isFailure();
    }

    /**
     * Check if this is an engagement event.
     */
    public function isEngagement(): bool
    {
        return $this->type->isEngagement();
    }
}
