<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Entities\Directory\DirectoryEntity;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Enums\ClaimKey;
use App\Validation\Directory\DirectoryNameValidator;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\InternalServerError;
use Core\Contracts\Api\Unauthorized;
use DateTime;

/**
 * @inheritdoc
 */
readonly class DirectoryService implements DirectoryServiceInterface
{
    function __construct(
        private DirectoryRepositoryInterface $directoryRepository,
        private CryptographicServiceInterface $cryptographicService,
        private AuthServiceInterface $authService
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getDirectories(): array | ApiResponse
    {
        $payload = $this->authService->validateAuthHeader();
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        // Get user ID by token claims
        $userId = $payload->getClaim(ClaimKey::Subject);

        return $this->directoryRepository->findByUserId($userId);
    }

    /**
     * @inheritdoc
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryEntity | ApiResponse
    {
        $payload = $this->authService->validateAuthHeader();
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        // Get user ID by token claims
        $userId = $payload->getClaim(ClaimKey::Subject);

        // Make sure to create default root directory for user
        $this->createRootDirectoryForUser($userId);

        $name = $request->name;
        if (!DirectoryNameValidator::validate($name))
        {
            return new BadRequest(
                "Directory name cannot be empty or contain any of the following special characters: " . DirectoryNameValidator::getInvalidCharactersFormatted());
        }

        // Check if the name is already used in the current directory
        $parentId = $request->parentId;
        $directoriesWithIdenticalName = $this->directoryRepository->findByNameForUserWithParentId($parentId, $userId, $name);
        if (count($directoriesWithIdenticalName) !== 0)
        {
            return new BadRequest("A directory with the name already exists");
        }

        $parentDirectory = $this->directoryRepository->findById($parentId);
        if (!$parentDirectory)
        {
            return new BadRequest("Parent directory does not exist");
        }

        $id = $this->cryptographicService->generateUniqueId();
        if (!$id)
        {
            return new InternalServerError("Unexpected server error");
        }

        $path = $parentDirectory->path . DIRECTORY_SEPARATOR . $name;
        $directoryEntity = new DirectoryEntity(
            $id,
            $parentId,
            $userId,
            $request->name,
            $path,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );

        $this->directoryRepository->tryAdd($directoryEntity);

        return $directoryEntity;
    }

    private function createRootDirectoryForUser(int $userId): void
    {
        $rootDirectory = DirectoryEntity::root($userId);
        if ($this->directoryRepository->findById($rootDirectory->id))
        {
            return;
        }

        $this->directoryRepository->tryAdd($rootDirectory);
    }
}