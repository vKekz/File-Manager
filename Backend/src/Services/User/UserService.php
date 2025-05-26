<?php

namespace Services\User;

use Contracts\User\RegisterUserResponse;

class UserService implements UserServiceInterface
{
    function getUsers(): array
    {
        return ["1", "2"];
    }

    function registerUser(mixed $request): RegisterUserResponse
    {
        return new RegisterUserResponse(rand());
    }
}