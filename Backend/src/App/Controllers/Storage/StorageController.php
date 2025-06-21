<?php

namespace App\Controllers\Storage;

use App\Services\Storage\StorageServiceInterface;
use Core\Attributes\Authorization\Authorize;
use Core\Attributes\Http\HttpGet;
use Core\Attributes\Parameter\QueryParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Controllers\ApiController;

/**
 * Represents the controller that is used to retrieve information about a user storage.
 */
#[Authorize]
class StorageController extends ApiController
{
    private const END_POINT = "api/storage";

    function __construct(private readonly StorageServiceInterface $storageService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpGet]
    function getDirectoryWithContent(#[QueryParameter] string $directoryId): ApiResponse
    {
        $response = $this->storageService->getDirectoryWithContent($directoryId);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }

    #[HttpGet("/search")]
    function search(#[QueryParameter] string $name): ApiResponse
    {
        return new Ok($name);
    }
}