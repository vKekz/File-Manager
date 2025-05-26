<?php

namespace Services\User;

use Contracts\User\RegisterUserRequest;
use Contracts\User\RegisterUserResponse;

interface UserServiceInterface
{
    function getUsers(): array;
    function registerUser(string $request): RegisterUserResponse;
}