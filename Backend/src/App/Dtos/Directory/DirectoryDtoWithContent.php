<?php

namespace App\Dtos\Directory;

use App\Dtos\File\FileDto;

/**
 * Represents a directory DTO including its content like directory children and files.
 */
readonly class DirectoryDtoWithContent
{
    /**
     * @param DirectoryDto[] $children
     * @param FileDto[] $files
     */
    function __construct(
        public string $id,
        public string $parentId,
        public string $name,
        public string $path,
        public bool $isRoot,
        public array $children,
        public array $files
    )
    {
    }
}