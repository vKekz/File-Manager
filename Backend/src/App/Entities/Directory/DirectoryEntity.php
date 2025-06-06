<?php

namespace App\Entities\Directory;

use DateTime;

/**
 * Represents the directory entity that can hold a list of files.
 */
readonly class DirectoryEntity
{
    function __construct(
        public int $id,
        public int $parentId,
        public int $userId,
        public string $name,
        public string $path,
        public string $createdAt
    )
    {
    }

    /**
     * Returns a directory entity that represents the root directory.
     */
    public static function root(int $userId): DirectoryEntity
    {
        return new self(0, -1, $userId, "Root", "$userId", (new DateTime())->format(DATE_ISO8601_EXPANDED));
    }

    public static function fromArray(array $data): DirectoryEntity
    {
        return new self($data["Id"], $data["ParentId"], $data["UserId"], $data["Name"], $data["Path"], $data["CreatedAt"]);
    }
}