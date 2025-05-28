<?php

namespace App\Contracts\User;

readonly class LoginUserRequest
{
    function __construct(public string $email, public string $password)
    {
    }

    public static function deserialize(string $json): LoginUserRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["email"], $decoded["password"]);
    }
}