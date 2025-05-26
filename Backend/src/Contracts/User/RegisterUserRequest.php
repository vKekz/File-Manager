<?php

namespace Contracts\User;

readonly class RegisterUserRequest
{
    function __construct(public string $email, public string $password)
    {
    }
}