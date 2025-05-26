<?php

namespace Contracts\User;

readonly class RegisterUserResponse
{
    function __construct(public string $id)
    {
    }
}