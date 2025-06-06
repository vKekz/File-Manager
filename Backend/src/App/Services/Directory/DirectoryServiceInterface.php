<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Entities\Directory\DirectoryEntity;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for managing directories.
 */
interface DirectoryServiceInterface
{
    /**
     * Returns an array of directories.
     */
    function getDirectories(): array | ApiResponse;
    /**
     * Creates a new directory.
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryEntity | ApiResponse;
}