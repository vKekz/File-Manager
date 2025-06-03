<?php

namespace App\Contracts\User;

readonly class UserRegisterRequest
{
    function __construct(public string $username, public string $email, public string $password)
    {
    }

    public static function deserialize(string $json): UserRegisterRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["username"], $decoded["email"], $decoded["password"]);
    }
}