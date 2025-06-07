<?php

namespace App\Entities\File;

/**
 * Represents the database entity for files.
 */
readonly class FileEntity
{
    function __construct(
        public string $id,
        public string $directoryId,
        public string $name,
        public string $hash
    )
    {
    }
}