<?php

namespace App\Services\File;

use App\Contracts\File\CreateFileRequest;
use App\Contracts\File\DeleteFileResponse;
use App\Dtos\File\FileDto;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\FileResponse;

/**
 * Represents the service that is used for managing files.
 */
interface FileServiceInterface
{
    /**
     * Returns an array of files that are present in the given directory.
     * @return FileDto[] | ApiResponse
     */
    function getFilesOfDirectory(string $directoryId): array | ApiResponse;
    /**
     * Creates a file for the given directory.
     */
    function createFile(CreateFileRequest $request): FileDto | ApiResponse;
    /**
     * Deletes a file.
     */
    function deleteFile(string $id): DeleteFileResponse | ApiResponse;
    /**
     * Prepares a file for download.
     */
    function getFile(string $id): FileResponse | ApiResponse;
}