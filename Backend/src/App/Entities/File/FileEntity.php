<?php

namespace App\Entities\File;

use App\Dtos\File\FileDto;

/**
 * Represents the database entity for files.
 */
class FileEntity
{
    function __construct(
        public readonly string $id,
        public readonly string $directoryId,
        public readonly string $userId,
        public string $name,
        public string $nameHash,
        public string $realHash,
        public string $hash,
        public int $size,
        public string $uploadedAt
    )
    {
    }

    public function toDto(): FileDto
    {
        return new FileDto($this->id, $this->name, $this->realHash, $this->size, $this->uploadedAt);
    }

    public static function fromArray(array $data): FileEntity
    {
        return new self($data["Id"], $data["DirectoryId"], $data["UserId"], $data["Name"], $data["NameHash"], $data["RealHash"], $data["Hash"], $data["Size"], $data["UploadedAt"]);
    }
}