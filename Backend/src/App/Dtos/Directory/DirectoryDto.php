<?php

namespace App\Dtos\Directory;

use App\Dtos\File\FileDto;

/**
 * Represents the DTO for directories.
 */
class DirectoryDto
{
    function __construct(
        public readonly string $id,
        public readonly string $parentId,
        public string $name,
        public string $path,
        public readonly string $createdAt,
        public readonly bool $isRoot,
    )
    {
    }

    /**
     * @param DirectoryDto[] $children
     * @param FileDto[] $files
     */
    public function withContent(array $children, array $files): DirectoryDtoWithContent
    {
        return new DirectoryDtoWithContent($this->id, $this->parentId, $this->name, $this->path, $this->createdAt, $this->isRoot, $children, $files);
    }

    public static function fromArray(array $data): DirectoryDto
    {
        return new self($data["Id"], $data["ParentId"], $data["Name"], $data["Path"], $data["CreatedAt"], $data["IsRoot"]);
    }
}