<?php

namespace App\Repositories\File;

use App\Entities\File\FileEntity;

/**
 * Represents the database repository for files.
 */
interface FileRepositoryInterface
{
    /**
     * Attempts to find a file entity by the given ID. Returns null on failure.
     */
    function findById(string $id): ?FileEntity;
    /**
     * Returns an array of file entities that are owned by the given user and are in the given directory.
     *
     * @return FileEntity[]
     */
    function findByDirectoryIdForUser(string $userId, string $directoryId): array;
    /**
     * Attempts to add a file entity to the repository. Returns true on success.
     */
    function tryAdd(FileEntity $entity): bool;
    /**
     * Attempts to remove a file entity from the repository. Returns true on success.
     */
    function tryRemove(string $id): bool;
}