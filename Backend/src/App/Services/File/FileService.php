<?php

namespace App\Services\File;

use App\Contracts\File\DeleteFileResponse;
use App\Contracts\File\UploadFileRequest;
use App\Dtos\File\FileDto;
use App\Entities\Directory\DirectoryEntity;
use App\Entities\File\FileEntity;
use App\Entities\User\UserEntity;
use App\Enums\FileReplacementBehaviour;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\FileSystem\FileSystemHandler;
use App\Services\FileSystem\FileSystemHandlerInterface;
use App\Validation\File\FileNameValidator;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\FileResponse;
use Core\Contracts\Api\FileStreamResponse;
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
    function uploadFile(UploadFileRequest $request): FileDto | ApiResponse
    {
        $file = $request->file;
        $name = $file->name;
        if (!FileNameValidator::validate($name))
        {
            return new BadRequest(
                "File name cannot exceed 255 characters or contain any of the following special characters: " . FileSystemHandler::getInvalidCharactersFormatted());
        }

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

        $nameHash = $this->cryptographicService->sign($name, CryptographicService::HASH_ALGORITHM);

        // Check if a file with the same name already exists
        $filesWithIdenticalName = $this->fileMapper->mapToEntities(
            $this->fileRepository->findByParentIdAndNameHashForUser($directoryId, $userId, $nameHash)
        );
        $hasFilesWithIdenticalName = count($filesWithIdenticalName) > 0;
        if ($user->settings->storageSettings->fileReplacementBehaviour === FileReplacementBehaviour::Replace &&
            $hasFilesWithIdenticalName)
        {
            return $this->replaceFile($user, $directory, $filesWithIdenticalName[0], $file);
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

        // Get file hash before encrypting it
        $realHash = $this->cryptographicService->signFile($file->tempPath);

        // Save uploaded file
        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $name;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);
        $this->fileSystemHandler->saveUploadedFile($file, $absolutePath, true, $user->privateKey);

        $fileEntity = new FileEntity(
            $id,
            $directoryId,
            $userId,
            $this->cryptographicService->encrypt($name, $user->privateKey),
            $nameHash,
            $realHash,
            $this->cryptographicService->signFile($absolutePath),
            $file->size,
            (new DateTime())->format(DATE_RFC3339)
        );

        if (!$this->fileRepository->tryAdd($fileEntity))
        {
            return new InternalServerError();
        }

        // Show unencrypted name to user when uploading a file
        $fileEntity->name = $name;

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

        $user = $this->httpContext->user;
        $userId = $user->id;
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
        $decryptedName = $this->cryptographicService->decrypt($fileEntity->name, $user->privateKey);
        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $decryptedName;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);
        $this->fileSystemHandler->deleteFile($absolutePath);

        return new DeleteFileResponse($id);
    }

    /**
     * @inheritdoc
     */
    function downloadFile(string $id): FileResponse | ApiResponse
    {
        $fileEntity = $this->fileRepository->findById($id);
        if (!$fileEntity)
        {
            return new BadRequest("File not found");
        }

        $user = $this->httpContext->user;
        $userId = $user->id;
        if ($fileEntity->userId !== $userId)
        {
            return new BadRequest("File is owned by another user");
        }

        $directory = $this->directoryRepository->findById($fileEntity->directoryId);
        if (!$directory)
        {
            return new BadRequest("Directory not found");
        }

        $decryptedName = $this->cryptographicService->decrypt($fileEntity->name, $user->privateKey);
        $relativePath = $userId . $directory->path . DIRECTORY_SEPARATOR . $decryptedName;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);

        // Check if file has been tampered
        $hash = $this->cryptographicService->signFile($absolutePath);
        if (!hash_equals($fileEntity->hash, $hash))
        {
            return new InternalServerError("File integrity verification failed: Signature mismatch");
        }

        return new FileStreamResponse(
            $this->cryptographicService->decryptFile($absolutePath, $user->privateKey),
            $decryptedName,
            $fileEntity->size
        );
    }

    private function replaceFile(UserEntity $user, DirectoryEntity $directory, FileEntity $file, UploadedFile $uploadedFile) : FileDto | ApiResponse
    {
        $realHash = $this->cryptographicService->signFile($uploadedFile->tempPath);
        $decryptedName = $this->cryptographicService->decrypt($file->name, $user->privateKey);
        $relativePath = $file->userId . $directory->path . DIRECTORY_SEPARATOR . $decryptedName;
        $absolutePath = $this->fileSystemHandler->getAbsolutePath($relativePath);

        // Replace file on file system
        $this->fileSystemHandler->saveUploadedFile($uploadedFile, $absolutePath, true, $user->privateKey);

        // Replace properties
        $file->realHash = $realHash;
        $file->hash = $this->cryptographicService->signFile($absolutePath);
        $file->size = $uploadedFile->size;
        $file->uploadedAt = (new DateTime())->format(DATE_RFC3339);

        // Update in database
        if (!$this->fileRepository->tryUpdate($file))
        {
            return new InternalServerError();
        }

        return $this->fileMapper->mapSingle($file);
    }
}