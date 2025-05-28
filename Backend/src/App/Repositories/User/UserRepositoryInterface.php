<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;

/**
 * Represents the repository interface for users.
 */
interface UserRepositoryInterface
{
    function getUsers(): array;
    /**
     * Attempts to find a user entity by the given ID.
     */
    function findById(int $id): ?UserEntity;
    /**
     * Attempts to add a user entity to the repository.
     */
    function tryAdd(UserEntity $entity): bool;
    /**
     * Attempts to remove a user entity to the repository.
     */
    function tryRemove(int $id): bool;
}