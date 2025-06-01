<?php

namespace App\Services\Hash;

use Core\Enums\EnvironmentKey;
use Core\Environment\Environment;
use Random\RandomException;

/**
 * @inheritdoc
 */
readonly class HashService implements HashServiceInterface
{
    /**
     * Default password hashing algorithm.
     */
    private const PASSWORD_ALGORITHM = PASSWORD_ARGON2ID;
    /**
     * Default hashing algorithm.
     */
    private const HASH_ALGORITHM = "sha3-512";

    function __construct(private Environment $environment)
    {
    }

    /**
     * @inheritdoc
     */
    function generateUniqueId(): int | false
    {
        try
        {
            return random_int(0, PHP_INT_MAX);
        } catch (RandomException)
        {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    function generatePasswordHash(string $input): string
    {
        return password_hash($input, self::PASSWORD_ALGORITHM);
    }

    /**
     * @inheritdoc
     */
    function verifyPassword(string $hash, string $userPassword): bool
    {
        return password_verify($userPassword, $hash);
    }

    /**
     * @inheritdoc
     */
    function generateHash(string $data): string
    {
        return hash_hmac(self::HASH_ALGORITHM, $data, $this->environment->get(EnvironmentKey::HASH_KEY));
    }
}