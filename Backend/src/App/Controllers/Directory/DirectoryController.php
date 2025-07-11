<?php

namespace App\Controllers\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Services\Directory\DirectoryServiceInterface;
use Core\Attributes\Authorization\Authorize;
use Core\Attributes\Http\HttpDelete;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\BodyParameter;
use Core\Attributes\Parameter\QueryParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Controllers\ApiController;

/**
 * Represents the controller that is used for directory management.
 */
#[Authorize]
class DirectoryController extends ApiController
{
    private const END_POINT = "api/directory";

    function __construct(private readonly DirectoryServiceInterface $directoryService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpPost]
    function createDirectory(#[BodyParameter] string $body): ApiResponse
    {
        $request = CreateDirectoryRequest::deserialize($body);
        $response = $this->directoryService->createDirectory($request);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }

    #[HttpDelete]
    function deleteDirectory(#[QueryParameter] string $id): ApiResponse
    {
        $response = $this->directoryService->deleteDirectory($id);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }
}