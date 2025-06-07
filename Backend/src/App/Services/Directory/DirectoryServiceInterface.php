<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Dtos\Directory\DirectoryDto;
use App\Entities\Directory\DirectoryEntity;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for managing directories.
 */
interface DirectoryServiceInterface
{
    /**
     * Returns the directory found by the given ID.
     */
    function getDirectoryById(string $id): DirectoryDto | ApiResponse;
    /**
     * Returns an array of directories that are the children of the parent directory.
     *
     * @return DirectoryDto[] | ApiResponse
     */
    function getChildrenOfParentDirectory(string $parentId): array | ApiResponse;
    /**
     * Creates a new directory.
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryEntity | ApiResponse;
}