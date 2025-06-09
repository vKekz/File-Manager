<?php

namespace App\Dtos\Directory;

use App\Entities\Directory\DirectoryEntity;

/**
 * Represents the DTO for directories.
 */
readonly class DirectoryDto
{
    /**
     * @param DirectoryDto[] $children
     */
    function __construct(public string $id, public string $parentId, public string $name, public string $path, public array $children = [])
    {
    }

    public static function fromArray(array $data): DirectoryDto
    {
        return new self($data["Id"], $data["ParentId"], $data["Name"], $data["Path"]);
    }

    public static function fromEntity(DirectoryEntity $entity): DirectoryDto
    {
        return new self($entity->id, $entity->parentId, $entity->name, $entity->path);
    }
}