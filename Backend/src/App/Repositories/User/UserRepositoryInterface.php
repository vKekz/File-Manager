<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;

/**
 * Represents the database repository for users.
 */
interface UserRepositoryInterface
{
    /**
     * Attempts to find a user entity by the given ID. Returns null on failure.
     */
    function findById(string $id): ?UserEntity;
    /**
     * Attempts to find a user entity by the given email hash. Returns null on failure.
     */
    function findByEmailHash(string $emailHash): ?UserEntity;
    /**
     * Attempts to update a users attributes. Returns true on success.
     */
    function tryUpdate(array $attributes, array $values, string $condition = ""): bool;
    /**
     * Attempts to add a user entity to the repository. Returns true on success.
     */
    function tryAdd(UserEntity $entity): bool;
    /**
     * Attempts to remove a user entity from the repository. Returns true on success.
     */
    function tryRemove(string $id): bool;
}