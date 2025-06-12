<?php

namespace App\Dtos\File;

/**
 * Represents the DTO for files.
 */
readonly class FileDto
{
    function __construct(
        public string $id,
        public string $name,
        public string $hash,
        public int $size,
        public string $uploadedAt
    )
    {
    }

    public static function fromArray(array $data): FileDto
    {
        return new self($data["Id"], $data["Name"], $data["Hash"], $data["Size"], $data["UploadedAt"]);
    }
}