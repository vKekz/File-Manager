<?php

namespace App\Mapper\Directory;

use App\Dtos\Directory\DirectoryDto;
use App\Entities\Directory\DirectoryEntity;
use App\Mapper\Mapper;

/**
 * Represents the mapper that is used to convert raw directory entities to DTOs.
 */
class DirectoryMapper extends Mapper
{
    /**
     * Converts a single directory entity to a DTO.
     *
     * @param DirectoryEntity $object
     */
    function mapSingle(mixed $object): DirectoryDto
    {
        return DirectoryDto::fromEntity($object);
    }

    /**
     * Converts an array of directory entities to DTO instances.
     *
     * @param DirectoryEntity[] $objects
     * @return DirectoryDto[]
     */
    function mapArray(array $objects): array
    {
        return array_map(
            fn(array $data): DirectoryDto => DirectoryDto::fromArray($data),
            $objects
        );
    }
}