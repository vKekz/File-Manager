<?php

namespace App\Dtos\Directory;

/**
 * Represents the DTO for directories.
 */
readonly class DirectoryDto
{
    function __construct(
        public string $id,
        public string $parentId,
        public string $name,
        public string $path,
        public bool $isRoot,
    )
    {
    }

    /**
     * @param DirectoryDto[] $children
     */
    public function withChildren(array $children): DirectoryDtoWithChildren
    {
        return new DirectoryDtoWithChildren($this->id, $this->parentId, $this->name, $this->path, $this->isRoot, $children);
    }

    public static function fromArray(array $data): DirectoryDto
    {
        return new self($data["Id"], $data["ParentId"], $data["Name"], $data["Path"], $data["IsRoot"]);
    }
}