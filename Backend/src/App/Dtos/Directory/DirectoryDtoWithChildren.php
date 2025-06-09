<?php

namespace App\Dtos\Directory;

/**
 * Represents a directory DTO including its children.
 */
readonly class DirectoryDtoWithChildren
{
    /**
     * @param DirectoryDto[] $children
     */
    function __construct(
        public string $id,
        public string $parentId,
        public string $name,
        public string $path,
        public bool $isRoot,
        public array $children
    )
    {
    }
}