<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Entities\Directory\DirectoryEntity;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\InternalServerError;
use DateTime;

/**
 * @inheritdoc
 */
readonly class DirectoryService implements DirectoryServiceInterface
{
    function __construct(
        private DirectoryRepositoryInterface $directoryRepository,
        private CryptographicServiceInterface $cryptographicService)
    {
    }

    /**
     * @inheritdoc
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryEntity | ApiResponse
    {
        $id = $this->cryptographicService->generateUniqueId();
        if (!$id)
        {
            return new InternalServerError("Unexpected server error");
        }

        $parentId = $request->parentId;
        $parentDirectory = $this->directoryRepository->findById($parentId);
        $path = $parentDirectory->path ?? "";

        return new DirectoryEntity(
            $id,
            $parentId,
            $request->name,
            $path,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );
    }
}