<?php

namespace App\Contracts\Storage;

use App\Dtos\Directory\DirectoryDto;
use App\Dtos\File\FileDto;

readonly class SearchStorageResponse
{
    /**
     * @param DirectoryDto[] $directories
     * @param FileDto[] $files
     */
    function __construct(public array $directories, public array $files)
    {
    }

    public static final function Empty(): SearchStorageResponse
    {
        return new self([], []);
    }
}