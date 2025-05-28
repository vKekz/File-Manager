<?php

namespace App\Services\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use Core\Contracts\Api\ServerErrorResponse;

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
    function getUserById(string $id): ?UserEntity;
    /**
     * Registers a new user, otherwise returns null if the user is already registered.
     */
    function registerUser(RegisterUserRequest $request): RegisterUserResponse | ServerErrorResponse;
    /**
     * Login a user.
     */
    function loginUser(LoginUserRequest $request);
    /**
     * Attempts to delete the user with the given ID.
     */
    function deleteUser(string $id): bool;
}