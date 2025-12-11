<?php

declare(strict_types=1);

namespace Lettr\Dto;

/**
 * Data Transfer Object for sending an email.
 */
final readonly class SendEmailData
{
    /**
     * @param  string  $from  The sender's email address
     * @param  array<string>  $to  The recipient email addresses
     * @param  string  $subject  The email subject
     * @param  string|null  $text  The plain-text body of the email
     * @param  string|null  $html  The HTML body of the email
     */
    public function __construct(
        public string $from,
        public array $to,
        public string $subject,
        public ?string $text = null,
        public ?string $html = null,
    ) {}

    /**
     * Create a new instance from an array.
     *
     * @param  array{
     *     from: string,
     *     to: array<string>,
     *     subject: string,
     *     text?: string|null,
     *     html?: string|null,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            from: $data['from'],
            to: $data['to'],
            subject: $data['subject'],
            text: $data['text'] ?? null,
            html: $data['html'] ?? null,
        );
    }

    /**
     * Convert the DTO to an array for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
        ];

        if ($this->text !== null) {
            $data['text'] = $this->text;
        }

        if ($this->html !== null) {
            $data['html'] = $this->html;
        }

        return $data;
    }
}
