<?php

namespace Contracts\User;

readonly class RegisterUserRequest
{
    function __construct(public string $email, public string $password)
    {
    }

    public static function fromJson(string $jsonString): RegisterUserRequest
    {
        $json = json_decode($jsonString, true);
        return new self($json->email, $json->password);
    }
}