<?php

namespace App\Services\FileSystem;

use Core\Contracts\File\UploadedFile;

/**
 * Represents the handler that is used to manage files and directories on the actual server file system.
 */
interface FileSystemHandlerInterface
{
    /**
     * Creates a new directory with at the given path.
     */
    function createDirectory(string $path): void;
    /**
     * Deletes a directory at the given path if it exists.
     *
     * Solution was found on stackoverflow: https://stackoverflow.com/a/3352564
     */
    function deleteDirectory(string $path): void;
    /**
     * Saves an uploaded file and moves it to the corresponding destination.
     */
    function saveUploadedFile(UploadedFile $source, string $destination, bool $encrypt = false, ?string $key = null): void;
    /**
     * Deletes a file at the given path.
     */
    function deleteFile(string $path): void;
    /**
     * Returns an absolute path where the given storage is located.
     */
    function getAbsolutePath(string $relativePath): string;
}