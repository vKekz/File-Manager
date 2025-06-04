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
    function findById(int $id): ?DirectoryEntity;
    /**
     * Attempts to add a directory entity to the repository. Returns true on success.
     */
    function tryAdd(DirectoryEntity $entity): bool;
    /**
     * Attempts to remove a directory entity from the repository. Returns true on success.
     */
    function tryRemove(int $id): bool;
}