<?php

namespace App\Services\User;

use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;

/**
 * Represents the service that is used for user operations.
 */
interface UserServiceInterface
{
    function getUsers();
    /**
     * Attempts to find a user by the given ID, otherwise returns null.
     */
    function getUserById(string $id): ?UserEntity;
    /**
     * Registers a new user.
     */
    function registerUser(RegisterUserRequest $request): RegisterUserResponse;
}