<?php

namespace App\Services\File;

use App\Contracts\File\CreateFileRequest;
use App\Contracts\File\DeleteFileResponse;
use App\Dtos\File\FileDto;
use App\Entities\Directory\DirectoryEntity;
use App\Entities\File\FileEntity;
use App\Enums\FileReplacementMode;
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
use Core\Contracts\File\UploadedFile;
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
        // Check if the directory exists and is owned by the user
        $directoryId = $request->directoryId;
        $directory = $this->directoryRepository->findById($directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory does not exist");
        }

        $user = $this->httpContext->user;
        $userId = $user->id;
        if ($directory->userId != $userId)
        {
            return new BadRequest("Directory is owned by another user");
        }

        $file = $request->file;
        $name = $file->name;

        // Check if a file with the same name already exists
        $filesWithIdenticalName = $this->fileMapper->mapToEntities(
            $this->fileRepository->findByParentIdAndNameForUser($directoryId, $userId, $name)
        );
        $hasFilesWithIdenticalName = count($filesWithIdenticalName);

        if ($user->settings->storageSettings->fileReplacementMode === FileReplacementMode::Replace &&
            $hasFilesWithIdenticalName)
        {
            return $this->replaceFile($directory, $filesWithIdenticalName[0], $file);
        }

        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError();
        }

        // Make sure to keep both files if there's an identical one
        if ($hasFilesWithIdenticalName)
        {
            $name = "(#" . substr($id, 0, 4) . ") " . $name;
        }

        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);

        // Save uploaded file
        $this->fileSystemHandler->saveUploadedFile($file, $absolutePath);

        $fileEntity = new FileEntity(
            $id,
            $directoryId,
            $userId,
            $name,
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

        if (!$this->fileRepository->tryRemove($id))
        {
            return new InternalServerError();
        }

        $directory = $this->directoryRepository->findById($fileEntity->directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory not found");
        }

        // Finally delete file on file system
        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $fileEntity->name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);
        $this->fileSystemHandler->deleteFile($absolutePath);

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

        $directory = $this->directoryRepository->findById($fileEntity->directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory not found");
        }

        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $fileEntity->name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);

        // Check if file has been tampered
        $hash = $this->cryptographicService->signFile($absolutePath);
        if (!hash_equals($fileEntity->hash, $hash))
        {
            return new InternalServerError("File integrity verification failed: Signature mismatch");
        }

        return new FileResponse(
            $absolutePath,
            $fileEntity->name,
            $fileEntity->size
        );
    }

    private function replaceFile(DirectoryEntity $directory, FileEntity $file, UploadedFile $uploadedFile) : FileDto | ApiResponse
    {
        $relativePath = $file->userId . $directory->path . DIRECTORY_SEPARATOR . $file->name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);

        // Replace file
        $this->fileSystemHandler->saveUploadedFile($uploadedFile, $absolutePath);

        // Replace properties
        $file->hash = $this->cryptographicService->signFile($absolutePath);
        $file->size = $uploadedFile->size;
        $file->uploadedAt = (new DateTime())->format(DATE_ISO8601_EXPANDED);

        // Update in database
        if (!$this->fileRepository->tryUpdate($file))
        {
            return new InternalServerError();
        }

        return $this->fileMapper->mapSingle($file);
    }
}