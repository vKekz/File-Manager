<?php

namespace App\Services\Storage;

use App\Contracts\Storage\SearchStorageResponse;
use App\Dtos\Directory\DirectoryDtoWithContent;
use App\Enums\StorageSearchBehaviour;
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

    /**
     * @inheritdoc
     */
    function search(string $name, string $directoryId): SearchStorageResponse
    {
        if (strlen($name) === 0)
        {
            return SearchStorageResponse::Empty();
        }

        // Get either all files or just the files in the current directory
        $user = $this->httpContext->user;
        if ($user->settings->storageSettings->storageSearchBehaviour === StorageSearchBehaviour::Expanded)
        {
            $directories = $this->directoryMapper->mapArray(
                $this->directoryRepository->findByUser($user->id)
            );
            $files = $this->fileMapper->mapArray(
                $this->fileRepository->findByUser($user->id)
            );
        }
        else
        {
            $directories = $this->directoryMapper->mapArray(
                $this->directoryRepository->findByParentIdForUser($user->id, $directoryId)
            );
            $files = $this->fileMapper->mapArray(
                $this->fileRepository->findByDirectoryIdForUser($user->id, $directoryId)
            );
        }

        $normalizedName = strtolower($name);

        // Filter directories by name
        $filteredDirectories = array_filter(
            $directories,
            function ($directory) use ($normalizedName, $user)
            {
                $directory->name = $this->cryptographicService->decrypt($directory->name, $user->privateKey);
                $isMatch = stripos($directory->name, $normalizedName) !== false;

                // Only decrypt path if there is a match
                if ($isMatch)
                {
                    $directory->path = $this->cryptographicService->decrypt($directory->path, $user->privateKey);
                }

                return $isMatch;
            }
        );

        // Filter files by name
        $filteredFiles = array_filter(
            $files,
            function ($file) use ($normalizedName, $user)
            {
                $file->name = $this->cryptographicService->decrypt($file->name, $user->privateKey);
                return stripos($file->name, $normalizedName) !== false;
            }
        );

        if (count($filteredDirectories) > 0)
        {
            $filteredDirectories = array_values($filteredDirectories);
        }

        if (count($filteredFiles) > 0)
        {
            $filteredFiles = array_values($filteredFiles);
        }

        return new SearchStorageResponse($filteredDirectories, $filteredFiles);
    }
}