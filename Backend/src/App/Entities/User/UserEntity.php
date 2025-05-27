<?php

namespace App\Entities\User;

/**
 * Represents a user entity.
 */
readonly class UserEntity
{
    public string $id;
    public string $userName;
    public string $email;
    public string $passwordHash;
}