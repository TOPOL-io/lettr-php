<?php

declare(strict_types=1);

namespace Lettr\Contracts;

/**
 * Interface for objects that can be converted to an array.
 */
interface Arrayable
{
    /**
     * Convert the object to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
