<?php

namespace App\Services\File;

use App\Contracts\File\UploadFileRequest;
use App\Dtos\File\FileDto;
use App\Entities\File\FileEntity;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
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
    function uploadFile(UploadFileRequest $request): FileDto | ApiResponse
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

        $fileEntity = new FileEntity(
            $id,
            $directoryId,
            $userId,
            $file->name,
            $directory->path,
            $this->cryptographicService->signFile($file->tempPath),
            $file->size,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->fileRepository->tryAdd($fileEntity))
        {
            return new InternalServerError();
        }

        return $this->fileMapper->mapSingle($fileEntity);
    }
}