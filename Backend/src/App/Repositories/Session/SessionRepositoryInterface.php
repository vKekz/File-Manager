<?php

namespace App\Repositories\Session;

use App\Entities\Session\SessionEntity;

/**
 * Represents the database repository for user sessions.
 */
interface SessionRepositoryInterface
{
    /**
     * Attempts to find a session entity by the given ID, otherwise returns null on failure.
     */
    function findById(int $id): ?SessionEntity;
    /**
     * Attempts to add a session entity to the repository. Returns true on success.
     */
    function tryAdd(SessionEntity $entity): bool;
    /**
     * Attempts to remove a session entity from the repository. Returns true on success.
     */
    function tryRemove(int $id): bool;
}