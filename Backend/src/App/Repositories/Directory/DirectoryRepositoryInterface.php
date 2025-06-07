<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;

/**
 * Represents the database repository for directories.
 */
interface DirectoryRepositoryInterface
{
    /**
     * Attempts to find a directory entity by the given ID. Returns null on failure.
     */
    function findById(string $id): ?DirectoryEntity;
    /**
     * Returns an array of directory entities that are owned by the given user and are in the given parent directory.
     *
     * @return DirectoryEntity[]
     */
    function findByUserId(string $userId, string $parentId): array;
    /**
     * Attempts to find the root directory entity for the given user.
     */
    function findRootForUser(string $userId): ?DirectoryEntity;
    /**
     * Returns an array of directory entities where each entity is in the given
     * parent directory, has the given name and is owned by the given user.
     *
     * Will be called when creating a new directory to check if the name is already used.
     *
     * @return DirectoryEntity[]
     */
    function findByNameForUserWithParentId(string $parentId, string $userId, string $name): array;
    /**
     * Attempts to add a directory entity to the repository. Returns true on success.
     */
    function tryAdd(DirectoryEntity $entity): bool;
    /**
     * Attempts to remove a directory entity from the repository. Returns true on success.
     */
    function tryRemove(string $id): bool;
}