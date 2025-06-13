<?php

namespace App\Services\File;

use App\Contracts\File\CreateFileRequest;
use App\Contracts\File\DeleteFileResponse;
use App\Dtos\File\FileDto;
use App\Entities\File\FileEntity;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\FileSystem\FileSystemHandlerInterface;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\FileResponse;
use Core\Contracts\Api\InternalServerError;
use DateTime;

/**
 * @inheritdoc
 */
readonly class FileService implements FileServiceInterface
{
    function __construct(
        private DirectoryRepositoryInterface $directoryRepository,
        private CryptographicServiceInterface $cryptographicService,
        private FileRepositoryInterface $fileRepository,
        private FileSystemHandlerInterface $fileSystemHandler,
        private FileMapper $fileMapper,
        private HttpContext $httpContext
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getFilesOfDirectory(string $directoryId): array | ApiResponse
    {
        // Check if the directory exists and is owned by the user
        $directory = $this->directoryRepository->findById($directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory does not exist");
        }

        $userId = $this->httpContext->user->id;
        if ($directory->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        return $this->fileMapper->mapArray($this->fileRepository->findByDirectoryIdForUser($userId, $directoryId));
    }

    /**
     * @inheritdoc
     */
    function createFile(CreateFileRequest $request): FileDto | ApiResponse
    {
        // TODO: Validation
        // TODO: Check if file exists and replace
        $file = $request->file;

        // Check if the directory exists and is owned by the user
        $directoryId = $request->directoryId;
        $directory = $this->directoryRepository->findById($directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory does not exist");
        }

        $userId = $this->httpContext->user->id;
        if ($directory->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError();
        }

        $name = $file->name;
        $path = $directory->path . DIRECTORY_SEPARATOR . $name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($userId . $path);

        // Save uploaded file
        $this->fileSystemHandler->saveUploadedFile($file, $absolutePath);

        $fileEntity = new FileEntity(
            $id,
            $directoryId,
            $userId,
            $name,
            $path,
            $this->cryptographicService->signFile($absolutePath),
            $file->size,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->fileRepository->tryAdd($fileEntity))
        {
            return new InternalServerError();
        }

        return $this->fileMapper->mapSingle($fileEntity);
    }

    /**
     * @inheritdoc
     */
    function deleteFile(string $id): DeleteFileResponse | ApiResponse
    {
        $fileEntity = $this->fileRepository->findById($id);
        if (!$fileEntity)
        {
            return new BadRequest("File not found");
        }

        $userId = $this->httpContext->user->id;
        if ($fileEntity->userId !== $userId)
        {
            return new BadRequest("File is owned by another user");
        }

        // Delete file on file system
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($userId . $fileEntity->path);
        $this->fileSystemHandler->deleteFile($absolutePath);

        if (!$this->fileRepository->tryRemove($id))
        {
            return new InternalServerError();
        }

        return new DeleteFileResponse($id);
    }

    /**
     * @inheritdoc
     */
    function getFile(string $id): FileResponse | ApiResponse
    {
        $fileEntity = $this->fileRepository->findById($id);
        if (!$fileEntity)
        {
            return new BadRequest("File not found");
        }

        $userId = $this->httpContext->user->id;
        if ($fileEntity->userId !== $userId)
        {
            return new BadRequest("File is owned by another user");
        }

        return new FileResponse(
            $this->fileSystemHandler->getAbsolutePath($userId . $fileEntity->path),
            $fileEntity->name,
            $fileEntity->size
        );
    }
}