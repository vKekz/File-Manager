<?php

namespace App\Controllers\File;

use App\Contracts\File\UploadFileRequest;
use App\Services\File\FileServiceInterface;
use Core\Attributes\Authorization\Authorize;
use Core\Attributes\Http\HttpGet;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\FileParameter;
use Core\Attributes\Parameter\PostParameter;
use Core\Attributes\Parameter\QueryParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Contracts\File\UploadedFile;
use Core\Controllers\ApiController;

/**
 * Represents the controller that is used for file management.
 */
#[Authorize]
class FileController extends ApiController
{
    private const END_POINT = "api/file";

    function __construct(private readonly FileServiceInterface $fileService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpGet]
    function getFilesOfDirectory(#[QueryParameter] string $directoryId): ApiResponse
    {
        $response = $this->fileService->getFilesOfDirectory($directoryId);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }

    #[HttpPost]
    function uploadFile(#[FileParameter] UploadedFile $file, #[PostParameter] string $directoryId): ApiResponse
    {
        $request = new UploadFileRequest($file, $directoryId);
        $response = $this->fileService->uploadFile($request);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }
}