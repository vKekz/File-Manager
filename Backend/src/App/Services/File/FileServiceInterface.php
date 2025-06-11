<?php

namespace App\Services\File;

use App\Contracts\File\UploadFileRequest;
use App\Dtos\File\FileDto;
use Core\Contracts\Api\ApiResponse;

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
     * Uploads a file to the given directory.
     */
    function uploadFile(UploadFileRequest $request): FileDto | ApiResponse;
}