<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Dtos\Directory\DirectoryDto;
use App\Dtos\Directory\DirectoryDtoWithChildren;
use App\Entities\Directory\DirectoryEntity;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for managing directories.
 */
interface DirectoryServiceInterface
{
    /**
     * Returns the directory found by the given ID including an array of its child directories.
     */
    function getDirectoryByIdWithChildren(string $id): DirectoryDtoWithChildren | ApiResponse;
    /**
     * Creates a new directory.
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryDto | ApiResponse;
}