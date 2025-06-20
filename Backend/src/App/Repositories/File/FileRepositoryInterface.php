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
     * Returns an array of file entities where each entity is in the given
     * directory, has the given name and is owned by the given user.
     *
     * Will be called when uploading a new file to check if a file with the same name already exists.
     *
     * @return FileEntity[]
     */
    function findByParentIdAndNameForUser(string $directoryId, string $userId, string $name): array;
    /**
     * Attempts to replace the given file entity's attributes. Returns true on success.
     */
    function tryUpdate(FileEntity $file): bool;
    /**
     * Attempts to add a file entity to the repository. Returns true on success.
     */
    function tryAdd(FileEntity $entity): bool;
    /**
     * Attempts to remove a file entity from the repository. Returns true on success.
     */
    function tryRemove(string $id): bool;
}