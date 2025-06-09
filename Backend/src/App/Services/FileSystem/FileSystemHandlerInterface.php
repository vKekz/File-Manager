<?php

namespace App\Services\FileSystem;

/**
 * Represents the handler that is used to manage files and directories on the actual server file system.
 */
interface FileSystemHandlerInterface
{
    /**
     * Creates a new directory with the given name at the given path.
     */
    function createDirectory(string $name, string $path): void;
}