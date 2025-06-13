<?php

namespace App\Services\Directory;

use App\Contracts\Directory\CreateDirectoryRequest;
use App\Contracts\Directory\DeleteDirectoryResponse;
use App\Dtos\Directory\DirectoryDto;
use App\Dtos\Directory\DirectoryDtoWithChildren;
use App\Entities\Directory\DirectoryEntity;
use App\Mapping\Directory\DirectoryMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\File\FileServiceInterface;
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
        private FileRepositoryInterface $fileRepository,
        private FileServiceInterface $fileService,
        private HttpContext $httpContext
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getDirectoryWithChildren(string $id): DirectoryDtoWithChildren | ApiResponse {
        $directoryEntity = $this->directoryRepository->findById($id);
        if ($directoryEntity == null)
        {
            return new NotFound("Directory not found");
        }

        $userId = $this->httpContext->user->id;
        if ($directoryEntity->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        $children = $this->directoryMapper->mapArray(
            $this->directoryRepository->findByParentIdForUser($userId, $id)
        );
        return $this->directoryMapper
            ->mapSingle($directoryEntity)
            ->withChildren($children);
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
                "Directory name cannot be empty or contain any of the following special characters: " . DirectoryNameValidator::getInvalidCharactersFormatted());
        }

        // Check if the parent directory exists and is owned by the user
        $parentId = $request->parentId;
        $parentDirectory = $this->directoryRepository->findById($parentId);
        if (!$parentDirectory)
        {
            return new BadRequest("Parent directory does not exist");
        }

        $userId = $this->httpContext->user->id;
        if ($parentDirectory->userId != $userId)
        {
            return new BadRequest("Parent directory is owned by another user");
        }

        // Check if the name is already used in the current directory
        $directoriesWithIdenticalName = $this->directoryRepository->findByParentIdAndNameForUser($parentId, $userId, $name);
        if (count($directoriesWithIdenticalName) !== 0)
        {
            return new BadRequest("A directory with the name already exists");
        }

        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError();
        }

        $path = $parentDirectory->path . DIRECTORY_SEPARATOR . $name;
        $directoryEntity = new DirectoryEntity(
            $id,
            $parentId,
            $userId,
            $name,
            $path,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );

        // Create entry in database
        if (!$this->directoryRepository->tryAdd($directoryEntity))
        {
            return new InternalServerError();
        }

        $this->fileSystemHandler->createDirectory($name, $path);

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

        $userId = $this->httpContext->user->id;
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

        // TODO: Delete directory from filesystem

        return new DeleteDirectoryResponse($id);
    }

    private function deleteDirectoryChildrenRecursively(DirectoryEntity $directoryEntity): void
    {
        $userId = $directoryEntity->userId;
        $id = $directoryEntity->id;

        $files = $this->fileRepository->findByDirectoryIdForUser($userId, $id);
        foreach ($files as $file)
        {
            $this->fileService->deleteFile($file->id);
        }

        // Apparently just saying that the return type is DirectoryEntity[] does not make the "compiler" happy
        // which is why I have to manually map the array of "DirectoryEntities" to DirectoryEntity[]
        $children = $this->directoryMapper->mapToEntities(
            $this->directoryRepository->findByParentIdForUser($userId, $id)
        );
        if (count($children) === 0)
        {
            return;
        }

        foreach ($children as $child)
        {
            $this->deleteDirectoryChildrenRecursively($child);
            $this->directoryRepository->tryRemove($child->id);
        }
    }
}