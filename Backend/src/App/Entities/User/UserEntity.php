<?php

namespace App\Entities\User;

/**
 * Represents a user entity.
 */
readonly class UserEntity
{
    function __construct(
        public string $id,
        public string $userName,
        public string $email,
        public string $passwordHash)
    {
    }

    public static function fromArray(array $data): UserEntity
    {
        // TODO: different naming policies are a problem
        return new self($data["Id"], $data["UserName"], $data["Email"], $data["PasswordHash"]);
    }
}