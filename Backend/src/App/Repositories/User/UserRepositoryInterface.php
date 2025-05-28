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
    function findById(string $id): ?UserEntity;
    /**
     * Attempts to save a user entity to the repository.
     */
    function trySave(UserEntity $entity): bool;
}