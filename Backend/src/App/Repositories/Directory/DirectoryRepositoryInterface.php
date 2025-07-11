<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;

/**
 * Represents the database repository for directories.
 */
interface DirectoryRepositoryInterface
{
    /**
     * Creates the root directory for the given user.
     */
    function createRootDirectoryForUser(string $userId): void;
    /**
     * Attempts to find a directory entity by the given ID. Returns null on failure.
     */
    function findById(string $id): ?DirectoryEntity;
    /**
     * Returns an array of directory entities that are owned by the given user and are in the given parent directory.
     *
     * @return DirectoryEntity[]
     */
    function findByParentIdForUser(string $userId, string $parentId): array;
    /**
     * Returns an array of directory entities where each entity is in the given
     * parent directory, matches given name hash and is owned by the given user.
     *
     * Will be called when creating a new directory to check if the name is already used.
     *
     * @return DirectoryEntity[]
     */
    function findByParentIdAndNameHashForUser(string $parentId, string $userId, string $nameHash): array;
    /**
     * Returns an array of directory entities that are owned by the given user.
     *
     * @return DirectoryEntity[]
     */
    function findByUser(string $userId): array;
    /**
     * Attempts to add a directory entity to the repository. Returns true on success.
     */
    function tryAdd(DirectoryEntity $entity): bool;
    /**
     * Attempts to remove a directory entity from the repository. Returns true on success.
     */
    function tryRemove(string $id): bool;
}