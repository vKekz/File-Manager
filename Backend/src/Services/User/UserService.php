<?php

namespace Services\User;

use Contracts\User\RegisterUserRequest;
use Contracts\User\RegisterUserResponse;

class UserService implements UserServiceInterface
{
    function getUsers(): array
    {
        return ["1", "2"];
    }

    function registerUser(RegisterUserRequest $request): RegisterUserResponse
    {
        return new RegisterUserResponse("");
    }
}