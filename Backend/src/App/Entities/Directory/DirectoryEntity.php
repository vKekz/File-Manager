<?php

namespace App\Entities\Directory;

/**
 * Represents the directory entity that can hold a list of files.
 */
readonly class DirectoryEntity
{
    function __construct(
        public int $id,
        public int $parentId,
        public string $name,
        public string $path,
        public string $createdAt
    )
    {
    }

    public static function fromArray(array $data): DirectoryEntity
    {
        return new self($data["Id"], $data["ParentId"], $data["Name"], $data["Path"], $data["CreatedAt"]);
    }
}