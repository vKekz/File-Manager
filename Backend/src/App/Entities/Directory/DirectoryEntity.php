<?php

namespace App\Entities\Directory;

use App\Dtos\Directory\DirectoryDto;

/**
 * Represents the database entity for directories.
 */
readonly class DirectoryEntity
{
    function __construct(
        public string $id,
        public string $parentId,
        public string $userId,
        public string $name,
        public string $path,
        public string $createdAt,
        public bool $isRoot = false
    )
    {
    }

    public function toDto(): DirectoryDto
    {
        return new DirectoryDto($this->id, $this->parentId, $this->name, $this->path, $this->isRoot);
    }

    public static function fromArray(array $data): DirectoryEntity
    {
        return new self($data["Id"], $data["ParentId"], $data["UserId"], $data["Name"], $data["Path"], $data["CreatedAt"], $data["IsRoot"]);
    }
}