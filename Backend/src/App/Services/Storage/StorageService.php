<?php

namespace App\Services\Storage;

use App\Dtos\Directory\DirectoryDtoWithContent;
use App\Mapping\Directory\DirectoryMapper;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\NotFound;

/**
 * @inheritdoc
 */
readonly class StorageService implements StorageServiceInterface
{
    function __construct(
        private CryptographicServiceInterface $cryptographicService,
        private DirectoryRepositoryInterface $directoryRepository,
        private FileRepositoryInterface $fileRepository,
        private DirectoryMapper $directoryMapper,
        private FileMapper $fileMapper,
        private HttpContext $httpContext,
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getDirectoryWithContent(string $directoryId): DirectoryDtoWithContent | ApiResponse
    {
        $directoryEntity = $this->directoryRepository->findById($directoryId);
        if ($directoryEntity == null)
        {
            return new NotFound("Directory not found");
        }

        $user = $this->httpContext->user;
        $userId = $user->id;
        if ($directoryEntity->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        // Decrypt directory names and paths
        $children = $this->directoryMapper->mapArray(
            $this->directoryRepository->findByParentIdForUser($userId, $directoryId)
        );
        foreach ($children as $child)
        {
            $child->name = $this->cryptographicService->decrypt($child->name, $user->privateKey);
            $child->path = $this->cryptographicService->decrypt($child->path, $user->privateKey);
        }

        // Decrypt file names
        $files = $this->fileMapper->mapArray(
            $this->fileRepository->findByDirectoryIdForUser($userId, $directoryId)
        );
        foreach ($files as $file)
        {
            $file->name = $this->cryptographicService->decrypt($file->name, $user->privateKey);
        }

        // Finally decrypt parent directory details
        $directoryEntity->name = $this->cryptographicService->decrypt($directoryEntity->name, $user->privateKey);
        $directoryEntity->path = $this->cryptographicService->decrypt($directoryEntity->path, $user->privateKey);

        return $this->directoryMapper->mapSingle($directoryEntity)->withContent($children, $files);
    }

    function search(string $name)
    {
        // TODO: Implement search() method.
    }
}