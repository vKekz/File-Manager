<?php

namespace App\Mapping\File;

use App\Dtos\File\FileDto;
use App\Entities\File\FileEntity;
use App\Mapping\Mapper;

/**
 * Represents the mapper that is used to convert raw file entities to DTOs.
 */
class FileMapper extends Mapper
{
    /**
     * Converts a single file entity to a DTO.
     *
     * @param FileEntity $object
     */
    function mapSingle(mixed $object): FileDto
    {
        return $object->toDto();
    }

    /**
     * Converts an array of file entities to DTO instances.
     *
     * @param FileEntity[] $objects
     * @return FileDto[]
     */
    function mapArray(array $objects): array
    {
        if (count($objects) === 0)
        {
            return [];
        }

        return array_map(
            fn(array $data): FileDto => FileDto::fromArray($data),
            $objects
        );
    }

    /**
     * @return FileEntity[]
     */
    function mapToEntities(array $entities): array
    {
        if (count($entities) === 0)
        {
            return [];
        }

        return array_map(
            fn(array $data): FileEntity => FileEntity::fromArray($data),
            $entities
        );
    }
}