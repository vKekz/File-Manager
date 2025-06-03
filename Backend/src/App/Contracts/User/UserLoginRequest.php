<?php

namespace App\Contracts\User;

readonly class UserLoginRequest
{
    function __construct(public string $email, public string $password)
    {
    }

    public static function deserialize(string $json): UserLoginRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["email"], $decoded["password"]);
    }
}