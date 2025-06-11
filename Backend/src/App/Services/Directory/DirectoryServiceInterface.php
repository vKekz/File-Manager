<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Contracts\Directory\DeleteDirectoryResponse;
use App\Dtos\Directory\DirectoryDto;
use App\Dtos\Directory\DirectoryDtoWithChildren;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for managing directories.
 */
interface DirectoryServiceInterface
{
    /**
     * Returns the directory found by the given ID including its child directories.
     */
    function getDirectoryWithChildren(string $id): DirectoryDtoWithChildren | ApiResponse;
    /**
     * Creates a new directory.
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryDto | ApiResponse;
    /**
     * Attempts to delete a directory recursively.
     */
    function deleteDirectory(string $id): DeleteDirectoryResponse | ApiResponse;
}