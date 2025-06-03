<?php

namespace App\Dtos\Users;

readonly class UserDto
{
    function __construct(public string $username, public string $email)
    {
    }
}