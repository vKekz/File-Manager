<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Dtos\Directory\DirectoryDto;
use App\Entities\Directory\DirectoryEntity;
use App\Mapping\Directory\DirectoryMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Enums\ClaimKey;
use App\Validation\Directory\DirectoryNameValidator;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\InternalServerError;
use Core\Contracts\Api\NotFound;
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
        private AuthServiceInterface $authService,
        private DirectoryMapper $directoryMapper
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getDirectoryById(string $id): DirectoryDto | ApiResponse
    {
        $payload = $this->authService->validateAuthHeader();
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        $directoryEntity = $this->directoryRepository->findById($id);
        if ($directoryEntity == null)
        {
            return new NotFound("Directory not found");
        }

        // Get user ID by token claims
        $userId = $this->cryptographicService->decrypt($payload->getClaim(ClaimKey::Subject));
        if ($directoryEntity->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        return $this->directoryMapper->mapSingle($directoryEntity);
    }

    function getDirectoryByIdWithChildren(string $id): DirectoryDto | ApiResponse {
    }

    /**
     * @inheritdoc
     */
    function getChildrenOfParentDirectory(string $parentId): array | ApiResponse
    {
        $payload = $this->authService->validateAuthHeader();
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        $parentDirectory = $this->directoryRepository->findById($parentId);
        if (!$parentDirectory)
        {
            return new BadRequest("Parent directory does not exist");
        }

        // Get user ID by token claims
        $userId = $this->cryptographicService->decrypt($payload->getClaim(ClaimKey::Subject));
        if ($parentDirectory->userId != $userId)
        {
            return new BadRequest("Parent directory is owned by another user");
        }

        return $this->directoryMapper->mapArray($this->directoryRepository->findByUserId($userId, $parentId));
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

        $name = $request->name;
        if (!DirectoryNameValidator::validate($name))
        {
            return new BadRequest(
                "Directory name cannot be empty or contain any of the following special characters: " . DirectoryNameValidator::getInvalidCharactersFormatted());
        }

        // Check if the parent directory exists and is owned by the user
        $parentId = $request->parentId;
        $parentDirectory = $this->directoryRepository->findById($parentId);
        if (!$parentDirectory)
        {
            return new BadRequest("Parent directory does not exist");
        }

        // Get user ID by token claims
        $userId = $this->cryptographicService->decrypt($payload->getClaim(ClaimKey::Subject));
        if ($parentDirectory->userId != $userId)
        {
            return new BadRequest("Parent directory is owned by another user");
        }

        // Check if the name is already used in the current directory
        $directoriesWithIdenticalName = $this->directoryRepository->findByNameForUserWithParentId($parentId, $userId, $name);
        if (count($directoriesWithIdenticalName) !== 0)
        {
            return new BadRequest("A directory with the name already exists");
        }

        $id = $this->cryptographicService->generateUuid();
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

    /**
     * @inheritdoc
     */
    function createRootDirectoryForUser(string $userId): void
    {
        if ($this->directoryRepository->findRootForUser($userId))
        {
            return;
        }

        // For simplicity, root directories will have the user ID as the primary key
        $rootDirectory = new DirectoryEntity(
            $userId,
            0,
            $userId,
            "root",
            "",
            (new DateTime())->format(DATE_ISO8601_EXPANDED),
            true
        );
        $this->directoryRepository->tryAdd($rootDirectory);
    }
}