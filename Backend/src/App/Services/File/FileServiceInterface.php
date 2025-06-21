<?php

namespace App\Services\File;

use App\Contracts\File\DeleteFileResponse;
use App\Contracts\File\UploadFileRequest;
use App\Dtos\File\FileDto;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\FileResponse;

/**
 * Represents the service that is used for managing files.
 */
interface FileServiceInterface
{
    /**
     * Uploads a file for the given directory. Replaces the existing one if names match.
     */
    function uploadFile(UploadFileRequest $request): FileDto | ApiResponse;
    /**
     * Deletes a file.
     */
    function deleteFile(string $id): DeleteFileResponse | ApiResponse;
    /**
     * Prepares a file for download.
     */
    function downloadFile(string $id): FileResponse | ApiResponse;
}