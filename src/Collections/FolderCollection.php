<?php

declare(strict_types=1);

namespace Lettr\Collections;

use Lettr\Dto\Folder\Folder;
use Lettr\Enums\TemplatePurpose;

/**
 * Collection of folders.
 *
 * @extends Collection<Folder>
 */
final readonly class FolderCollection extends Collection
{
    /**
     * Get the first folder in the collection.
     */
    public function first(): ?Folder
    {
        return $this->items[0] ?? null;
    }

    /**
     * Find a folder by ID.
     */
    public function findById(int $id): ?Folder
    {
        foreach ($this->items as $folder) {
            if ($folder->id === $id) {
                return $folder;
            }
        }

        return null;
    }

    /**
     * Find a folder by name.
     */
    public function findByName(string $name): ?Folder
    {
        foreach ($this->items as $folder) {
            if ($folder->name === $name) {
                return $folder;
            }
        }

        return null;
    }

    /**
     * Filter folders by module.
     */
    public function filterByPurpose(TemplatePurpose $purpose): self
    {
        return new self(
            array_filter(
                $this->items,
                static fn (Folder $folder): bool => $folder->purpose === $purpose
            )
        );
    }
}
