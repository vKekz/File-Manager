<?php

namespace App\Entities\User;

/**
 * Represents the database entity for users.
 */
class UserEntity
{
    function __construct(
        public readonly string $id,
        public string $username,
        public string $email,
        public readonly string $emailHash,
        public string $passwordHash,
        public string $privateKey,
        public readonly string $createdAt,
        public readonly UserSettings $settings = new UserSettings()
    )
    {
    }

    public static function fromArray(array $data): UserEntity
    {
        return new self(
            $data["Id"],
            $data["UserName"],
            $data["Email"],
            $data["EmailHash"],
            $data["PasswordHash"],
            $data["PrivateKey"],
            $data["CreatedAt"],
            UserSettings::fromArray(json_decode($data["Settings"], true))
        );
    }
}