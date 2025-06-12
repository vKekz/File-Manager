<?php

namespace Core\Contracts\File;

/**
 * Represents a file that has been uploaded via FormData and captured using the built-in $_FILES variable.
 *
 * @see https://www.php.net/manual/en/reserved.variables.files.php
 */
readonly class UploadedFile
{
    function __construct(
        public string $name,
        public string $type,
        public string $tempPath,
        public int $size
    )
    {
    }

    /**
     * @see https://www.php.net/manual/en/features.file-upload.post-method.php
     */
    public static function fromArray(array $data): UploadedFile
    {
        return new self($data["name"], $data["type"], $data["tmp_name"], $data["size"]);
    }
}