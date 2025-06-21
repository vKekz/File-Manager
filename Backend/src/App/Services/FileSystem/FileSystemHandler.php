<?php

namespace App\Services\FileSystem;

use App\Services\Cryptographic\CryptographicServiceInterface;
use Core\Contracts\File\UploadedFile;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @inheritdoc
 */
class FileSystemHandler implements FileSystemHandlerInterface
{
    private const USER_STORAGE_PATH = "Storage" . DIRECTORY_SEPARATOR . "Users";

    function __construct(private readonly CryptographicServiceInterface $cryptographicService)
    {
    }

    /**
     * @inheritdoc
     */
    function createDirectory(string $path): void
    {
        if (file_exists($path))
        {
            return;
        }

        mkdir($path, recursive: true);
    }

    /**
     * @inheritdoc
     */
    function deleteDirectory(string $path): void
    {
        if (!is_dir($path))
        {
            return;
        }

        $children = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        // Delete directory children first
        foreach ($children as $child)
        {
            if ($child->isDir())
            {
                rmdir($child->getRealPath());
            }
            else
            {
                unlink($child->getRealPath());
            }
        }

        // Finally delete main directory
        rmdir($path);
    }

    /**
     * @inheritdoc
     */
    function saveUploadedFile(UploadedFile $source, string $destination, bool $encrypt = false, ?string $key = null): void
    {
        if (!file_exists($source->tempPath))
        {
            return;
        }

        // Create directory if it does not exist
        $this->createDirectory(dirname($destination));

        if ($encrypt)
        {
            $this->cryptographicService->encryptFile($source->tempPath, $destination, $key);
        }
        else
        {
            move_uploaded_file($source->tempPath, $destination);
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteFile(string $path): void
    {
        if (!file_exists($path))
        {
            return;
        }

        unlink($path);
    }

    /**
     * @inheritdoc
     */
    function getAbsolutePath(string $relativePath): string
    {
        return dirname(getcwd(), 2) . DIRECTORY_SEPARATOR . self::USER_STORAGE_PATH . DIRECTORY_SEPARATOR . $relativePath;
    }
}