<?php

declare(strict_types=1);

namespace Lettr\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Lettr\Dto\Email\EmailEvent;
use Lettr\Enums\EventType;
use Traversable;

/**
 * Collection of email events.
 *
 * @implements IteratorAggregate<int, EmailEvent>
 */
final readonly class EmailEventCollection implements Countable, IteratorAggregate
{
    /**
     * @var array<int, EmailEvent>
     */
    private array $items;

    /**
     * @param  array<int, EmailEvent>  $items
     */
    private function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    /**
     * Create a new collection from an array of events.
     *
     * @param  array<int, EmailEvent>  $items
     */
    public static function from(array $items): self
    {
        return new self($items);
    }

    /**
     * Create an empty collection.
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /**
     * Get all events.
     *
     * @return array<int, EmailEvent>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Filter events by type.
     */
    public function filterByType(EventType $type): self
    {
        return new self(
            array_filter(
                $this->items,
                static fn (EmailEvent $event): bool => $event->type === $type
            )
        );
    }

    /**
     * Get all successful delivery events.
     */
    public function successful(): self
    {
        return new self(
            array_filter(
                $this->items,
                static fn (EmailEvent $event): bool => $event->type->isSuccess()
            )
        );
    }

    /**
     * Get all failed events.
     */
    public function failed(): self
    {
        return new self(
            array_filter(
                $this->items,
                static fn (EmailEvent $event): bool => $event->type->isFailure()
            )
        );
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return Traversable<int, EmailEvent>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
