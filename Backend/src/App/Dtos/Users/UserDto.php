<?php

namespace App\Dtos\Users;

readonly class UserDto
{
    function __construct(public int $id, public string $username, public string $email)
    {
    }
}