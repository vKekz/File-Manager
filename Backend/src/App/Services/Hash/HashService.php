<?php

namespace App\Services\Hash;

class HashService implements HashServiceInterface
{
    private const PASSWORD_ALGORITHM = PASSWORD_ARGON2ID;

    function generatePasswordHash(string $input): string
    {
        return password_hash($input, self::PASSWORD_ALGORITHM);
    }

    function verifyPassword(string $hash, string $userPassword): bool
    {
        return password_verify($userPassword, $hash);
    }
}