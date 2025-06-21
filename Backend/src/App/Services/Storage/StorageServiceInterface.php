<?php

namespace App\Services\Storage;

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
    function search(string $name);
}