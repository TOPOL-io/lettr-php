<?php

declare(strict_types=1);

namespace Lettr\Dto\Email;

use Lettr\Contracts\Arrayable;

/**
 * Email sending options.
 */
final readonly class EmailOptions implements Arrayable
{
    public function __construct(
        public bool $clickTracking = true,
        public bool $openTracking = true,
        public bool $transactional = false,
        public bool $inlineCss = true,
        public bool $performSubstitutions = true,
    ) {}

    /**
     * Create from an array.
     *
     * @param  array{
     *     click_tracking?: bool,
     *     open_tracking?: bool,
     *     transactional?: bool,
     *     inline_css?: bool,
     *     perform_substitutions?: bool,
     * }  $data
     */
    public static function from(array $data): self
    {
        return new self(
            clickTracking: $data['click_tracking'] ?? true,
            openTracking: $data['open_tracking'] ?? true,
            transactional: $data['transactional'] ?? false,
            inlineCss: $data['inline_css'] ?? true,
            performSubstitutions: $data['perform_substitutions'] ?? true,
        );
    }

    /**
     * Create default options.
     */
    public static function default(): self
    {
        return new self;
    }

    /**
     * Create options for transactional emails.
     */
    public static function transactional(): self
    {
        return new self(transactional: true);
    }

    /**
     * Create new instance with click tracking enabled/disabled.
     */
    public function withClickTracking(bool $enabled = true): self
    {
        return new self(
            clickTracking: $enabled,
            openTracking: $this->openTracking,
            transactional: $this->transactional,
            inlineCss: $this->inlineCss,
            performSubstitutions: $this->performSubstitutions,
        );
    }

    /**
     * Create new instance with open tracking enabled/disabled.
     */
    public function withOpenTracking(bool $enabled = true): self
    {
        return new self(
            clickTracking: $this->clickTracking,
            openTracking: $enabled,
            transactional: $this->transactional,
            inlineCss: $this->inlineCss,
            performSubstitutions: $this->performSubstitutions,
        );
    }

    /**
     * Create new instance as transactional.
     */
    public function asTransactional(bool $transactional = true): self
    {
        return new self(
            clickTracking: $this->clickTracking,
            openTracking: $this->openTracking,
            transactional: $transactional,
            inlineCss: $this->inlineCss,
            performSubstitutions: $this->performSubstitutions,
        );
    }

    /**
     * @return array{
     *     click_tracking: bool,
     *     open_tracking: bool,
     *     transactional: bool,
     *     inline_css: bool,
     *     perform_substitutions: bool,
     * }
     */
    public function toArray(): array
    {
        return [
            'click_tracking' => $this->clickTracking,
            'open_tracking' => $this->openTracking,
            'transactional' => $this->transactional,
            'inline_css' => $this->inlineCss,
            'perform_substitutions' => $this->performSubstitutions,
        ];
    }
}
