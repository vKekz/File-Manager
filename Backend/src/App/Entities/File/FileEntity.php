<?php

namespace App\Entities\File;

/**
 * Represents a file entity.
 */
readonly class FileEntity
{
    function __construct(public int $id, public string $path)
    {
    }
}