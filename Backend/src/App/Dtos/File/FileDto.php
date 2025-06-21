<?php

namespace App\Dtos\File;

/**
 * Represents the DTO for files.
 */
class FileDto
{
    function __construct(
        public readonly string $id,
        public string $name,
        public readonly string $hash,
        public readonly int $size,
        public readonly string $uploadedAt
    )
    {
    }

    public static function fromArray(array $data): FileDto
    {
        return new self($data["Id"], $data["Name"], $data["RealHash"], $data["Size"], $data["UploadedAt"]);
    }
}