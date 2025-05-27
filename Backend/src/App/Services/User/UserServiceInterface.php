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
    function getUserById(string $id): ?UserEntity;
    function registerUser(RegisterUserRequest $request): RegisterUserResponse;
}