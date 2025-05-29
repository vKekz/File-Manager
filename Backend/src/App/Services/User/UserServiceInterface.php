<?php

namespace App\Services\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for user operations.
 */
interface UserServiceInterface
{
    /**
     * Returns an array of all users.
     */
    function getUsers(): array;
    /**
     * Attempts to find a user by the given ID, otherwise returns null.
     */
    function getUserById(int $id): ?UserEntity;
    /**
     * Registers a new user.
     */
    function registerUser(RegisterUserRequest $request): RegisterUserResponse | ApiResponse;
    /**
     * Login a user.
     */
    function loginUser(LoginUserRequest $request);
    /**
     * Attempts to delete the user with the given ID.
     */
    function deleteUser(int $id): bool;
}