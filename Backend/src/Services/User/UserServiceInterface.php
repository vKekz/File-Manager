<?php

namespace Services\User;

use Contracts\User\RegisterUserRequest;
use Contracts\User\RegisterUserResponse;
use Entities\User\UserEntity;

/**
 * Represents the service that is used for user operations.
 */
interface UserServiceInterface
{
    function getUserById(string $id): ?UserEntity;
    function registerUser(RegisterUserRequest $request): RegisterUserResponse;
}