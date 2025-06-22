<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Contracts\Directory\DeleteDirectoryResponse;
use App\Dtos\Directory\DirectoryDto;
use App\Entities\Directory\DirectoryEntity;
use App\Mapping\Directory\DirectoryMapper;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\File\FileServiceInterface;
use App\Services\FileSystem\FileSystemHandler;
use App\Services\FileSystem\FileSystemHandlerInterface;
use App\Validation\Directory\DirectoryNameValidator;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\InternalServerError;
use Core\Contracts\Api\NotFound;
use DateTime;

/**
 * @inheritdoc
 */
readonly class DirectoryService implements DirectoryServiceInterface
{
    function __construct(
        private DirectoryRepositoryInterface $directoryRepository,
        private CryptographicServiceInterface $cryptographicService,
        private FileSystemHandlerInterface $fileSystemHandler,
        private DirectoryMapper $directoryMapper,
        private FileMapper $fileMapper,
        private FileRepositoryInterface $fileRepository,
        private FileServiceInterface $fileService,
        private HttpContext $httpContext
    )
    {
    }

    /**
     * @inheritdoc
     */
    function createDirectory(CreateDirectoryRequest $request): DirectoryDto | ApiResponse
    {
        $name = $request->name;
        if (!DirectoryNameValidator::validate($name))
        {
            return new BadRequest(
                "Directory name cannot be empty or contain any of the following special characters: " . FileSystemHandler::getInvalidCharactersFormatted());
        }

        // Check if the parent directory exists and is owned by the user
        $parentId = $request->parentId;
        $parentDirectory = $this->directoryRepository->findById($parentId);
        if (!$parentDirectory)
        {
            return new BadRequest("Parent directory does not exist");
        }

        $user = $this->httpContext->user;
        $userId = $user->id;
        if ($parentDirectory->userId != $userId)
        {
            return new BadRequest("Parent directory is owned by another user");
        }

        // Check if the name is already used in the current directory
        $nameHash = $this->cryptographicService->sign($name, CryptographicService::HASH_ALGORITHM);
        $directoriesWithIdenticalName = $this->directoryRepository->findByParentIdAndNameHashForUser($parentId, $userId, $nameHash);
        if (count($directoriesWithIdenticalName) !== 0)
        {
            return new BadRequest("A directory with the name already exists");
        }

        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError();
        }

        $decryptedParentPath = $this->cryptographicService->decrypt($parentDirectory->path, $user->privateKey);
        $path = $decryptedParentPath . DIRECTORY_SEPARATOR . $name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($userId . $path);

        // Create real directory on file system
        $this->fileSystemHandler->createDirectory($absolutePath);

        $directoryEntity = new DirectoryEntity(
            $id,
            $parentId,
            $userId,
            $this->cryptographicService->encrypt($name, $user->privateKey),
            $nameHash,
            $this->cryptographicService->encrypt($path, $user->privateKey),
            (new DateTime())->format(DATE_RFC3339)
        );

        // Create entry in database
        if (!$this->directoryRepository->tryAdd($directoryEntity))
        {
            return new InternalServerError();
        }

        // Show unencrypted name & path to user when creating the directory
        $directoryEntity->name = $name;
        $directoryEntity->path = $path;

        return $this->directoryMapper->mapSingle($directoryEntity);
    }

    /**
     * @inheritdoc
     */
    function deleteDirectory(string $id): DeleteDirectoryResponse | ApiResponse
    {
        $directoryEntity = $this->directoryRepository->findById($id);
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

        // Root user directory cannot be deleted
        if ($directoryEntity->isRoot)
        {
            return new BadRequest("Cannot delete root directory");
        }

        $this->deleteDirectoryChildrenRecursively($directoryEntity);

        // Finally delete parent directory
        if (!$this->directoryRepository->tryRemove($id))
        {
            return new InternalServerError();
        }

        $decryptedPath = $this->cryptographicService->decrypt($directoryEntity->path, $user->privateKey);
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($userId . $decryptedPath);
        $this->fileSystemHandler->deleteDirectory($absolutePath);

        return new DeleteDirectoryResponse($id);
    }

    /**
     * Deletes the children of a directory entity including its files and directories in the database and file system.
     */
    private function deleteDirectoryChildrenRecursively(DirectoryEntity $directoryEntity): void
    {
        $userId = $directoryEntity->userId;
        $id = $directoryEntity->id;

        // Delete files of directory
        $files = $this->fileMapper->mapToEntities(
            $this->fileRepository->findByDirectoryIdForUser($userId, $id)
        );
        foreach ($files as $file)
        {
            $this->fileService->deleteFile($file->id);
        }

        // Apparently just saying that the return type is DirectoryEntity[] does not make the "compiler" happy
        // which is why I have to manually map the array of "DirectoryEntities" to DirectoryEntity[]
        //
        // Same with files above btw...
        $children = $this->directoryMapper->mapToEntities(
            $this->directoryRepository->findByParentIdForUser($userId, $id)
        );
        if (count($children) === 0)
        {
            return;
        }

        // Call recursively for children
        foreach ($children as $child)
        {
            $this->deleteDirectoryChildrenRecursively($child);
            $this->directoryRepository->tryRemove($child->id);
        }
    }
}