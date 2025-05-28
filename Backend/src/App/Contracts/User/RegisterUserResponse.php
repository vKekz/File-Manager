<?php

namespace App\Contracts\User;

readonly class RegisterUserResponse
{
    function __construct(public int $id)
    {
    }
}