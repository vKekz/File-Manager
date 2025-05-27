<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;

/**
 * Represents the repository interface for users.
 */
interface UserRepositoryInterface
{
    /**
     * Attempts to find a user entity by the given ID.
     */
    function findById(string $id): ?UserEntity;
    /**
     * Saves a user entity to the repository.
     */
    function save(UserEntity $entity): void;
}