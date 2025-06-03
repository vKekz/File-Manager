<?php

namespace App\Entities\User;

/**
 * Represents a user entity.
 */
readonly class UserEntity
{
    function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $passwordHash,
        public string $createdAt,
    )
    {
    }

    public static function fromArray(array $data): UserEntity
    {
        return new self($data["Id"], $data["UserName"], $data["Email"], $data["PasswordHash"], $data["CreatedAt"]);
    }
}