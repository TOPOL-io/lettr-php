<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Contracts\Arrayable;
use Lettr\ValueObjects\Cursor;
use Lettr\ValueObjects\EmailAddress;
use Lettr\ValueObjects\Timestamp;

/**
 * Filter parameters for listing emails/events.
 */
final readonly class ListEmailsFilter implements Arrayable
{
    public function __construct(
        public ?int $perPage = null,
        public ?Cursor $cursor = null,
        public ?EmailAddress $recipient = null,
        public ?Timestamp $from = null,
        public ?Timestamp $to = null,
    ) {}

    /**
     * Create a new filter.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Set items per page.
     */
    public function perPage(int $perPage): self
    {
        return new self(
            perPage: $perPage,
            cursor: $this->cursor,
            recipient: $this->recipient,
            from: $this->from,
            to: $this->to,
        );
    }

    /**
     * Set the pagination cursor.
     */
    public function cursor(string|Cursor $cursor): self
    {
        return new self(
            perPage: $this->perPage,
            cursor: $cursor instanceof Cursor ? $cursor : new Cursor($cursor),
            recipient: $this->recipient,
            from: $this->from,
            to: $this->to,
        );
    }

    /**
     * Filter by recipient email.
     */
    public function forRecipient(string|EmailAddress $recipient): self
    {
        return new self(
            perPage: $this->perPage,
            cursor: $this->cursor,
            recipient: $recipient instanceof EmailAddress ? $recipient : new EmailAddress($recipient),
            from: $this->from,
            to: $this->to,
        );
    }

    /**
     * Filter from a specific date.
     */
    public function fromDate(string|\DateTimeImmutable|Timestamp $from): self
    {
        return new self(
            perPage: $this->perPage,
            cursor: $this->cursor,
            recipient: $this->recipient,
            from: Timestamp::from($from),
            to: $this->to,
        );
    }

    /**
     * Filter to a specific date.
     */
    public function toDate(string|\DateTimeImmutable|Timestamp $to): self
    {
        return new self(
            perPage: $this->perPage,
            cursor: $this->cursor,
            recipient: $this->recipient,
            from: $this->from,
            to: Timestamp::from($to),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $params = [];

        if ($this->perPage !== null) {
            $params['per_page'] = $this->perPage;
        }

        if ($this->cursor !== null) {
            $params['cursor'] = (string) $this->cursor;
        }

        if ($this->recipient !== null) {
            $params['recipient'] = $this->recipient->address;
        }

        if ($this->from !== null) {
            $params['from'] = $this->from->toIso8601();
        }

        if ($this->to !== null) {
            $params['to'] = $this->to->toIso8601();
        }

        return $params;
    }

    /**
     * Check if any filters are set.
     */
    public function hasFilters(): bool
    {
        return $this->perPage !== null
            || $this->cursor !== null
            || $this->recipient !== null
            || $this->from !== null
            || $this->to !== null;
    }
}
