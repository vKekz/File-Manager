<?php

namespace App\Dtos\Users;

/**
 * Represents the DTO for users.
 */
readonly class UserDto
{
    function __construct(public string $id, public string $username, public string $email)
    {
    }
}