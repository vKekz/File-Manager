<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;

/**
 * Represents the database repository for users.
 */
interface UserRepositoryInterface
{
    /**
     * Returns an array of all users.
     */
    function getUsers(): array;
    /**
     * Attempts to find a user entity by the given ID. Returns null on failure.
     */
    function findById(int $id): ?UserEntity;
    /**
     * Attempts to find a user entity by the given email. Returns null on failure.
     */
    function findByEmail(string $email): ?UserEntity;
    /**
     * Attempts to add a user entity to the repository. Returns true on success.
     */
    function tryAdd(UserEntity $entity): bool;
    /**
     * Attempts to remove a user entity from the repository. Returns true on success.
     */
    function tryRemove(int $id): bool;
}