<?php

namespace App\Entities\User;

use Core\Utilities\Deserializer;

/**
 * Represents a user entity.
 */
class UserEntity extends Deserializer
{
    public string $id;
    public string $userName;
    public string $email;
    public string $passwordHash;

    public static function fromQuery(array $data): UserEntity
    {
        // TODO: different naming policies are a problem

        $user = new self();
        $user->id = $data["Id"];
        $user->userName = $data["UserName"];
        $user->email = $data["Email"];
        $user->passwordHash = $data["PasswordHash"];

        return $user;
    }
}