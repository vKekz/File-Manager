<?php

namespace App\Entities\File;

use App\Dtos\File\FileDto;

/**
 * Represents the database entity for files.
 */
readonly class FileEntity
{
    function __construct(
        public string $id,
        public string $directoryId,
        public string $userId,
        public string $name,
        public string $path,
        public string $hash,
        public int $size,
        public string $uploadedAt
    )
    {
    }

    public function toDto(): FileDto
    {
        return new FileDto($this->id, $this->name, $this->hash, $this->size, $this->uploadedAt);
    }

    public static function fromArray(array $data): FileEntity
    {
        return new self($data["Id"], $data["DirectoryId"], $data["UserId"], $data["Name"], $data["Path"], $data["Hash"], $data["Size"], $data["UploadedAt"]);
    }
}