<?php

namespace App\Contracts\User;

use Core\Utilities\JsonDeserializableInterface;

readonly class RegisterUserRequest implements JsonDeserializableInterface
{
    function __construct(public string $email, public string $password)
    {
    }

    public static function deserialize(string $json): RegisterUserRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["email"], $decoded["password"]);
    }
}