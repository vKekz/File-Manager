<?php

namespace App\Entities\Directory;

use App\Dtos\Directory\DirectoryDto;

/**
 * Represents the database entity for directories.
 */
class DirectoryEntity
{
    function __construct(
        public readonly string $id,
        public readonly string $parentId,
        public readonly string $userId,
        public string $name,
        public readonly string $nameHash,
        public string $path,
        public readonly string $createdAt,
        public readonly bool $isRoot = false
    )
    {
    }

    public function toDto(): DirectoryDto
    {
        return new DirectoryDto($this->id, $this->parentId, $this->name, $this->path, $this->isRoot);
    }

    public static function fromArray(array $data): DirectoryEntity
    {
        return new self($data["Id"], $data["ParentId"], $data["UserId"], $data["Name"], $data["NameHash"], $data["Path"], $data["CreatedAt"], $data["IsRoot"]);
    }
}