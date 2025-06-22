<?php

namespace App\Services\Storage;

use App\Contracts\Storage\SearchStorageResponse;
use App\Dtos\Directory\DirectoryDtoWithContent;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used to retrieve information about a user storage.
 */
interface StorageServiceInterface
{
    /**
     * Returns a directory found by the given ID including its content.
     */
    function getDirectoryWithContent(string $directoryId): DirectoryDtoWithContent | ApiResponse;
    /**
     * Returns an array of directories and files that have a similar matching name.
     */
    function search(string $name, string $directoryId): SearchStorageResponse;
}