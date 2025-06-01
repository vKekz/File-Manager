<?php

namespace App\Services\Hash;

/**
 * Represents the service that is used for cryptographically secure functions.
 */
interface HashServiceInterface
{
    /**
     * Attempts to generate a unique number ID, otherwise returns false on failure.
     */
    function generateUniqueId(): int | false;
    /**
     * Generates a password hash for the given input.
     */
    function generatePasswordHash(string $input): string;
    /**
     * Verifies that the given user password matches its hash.
     */
    function verifyPassword(string $hash, string $userPassword): bool;
    /**
     * Generates a keyed hash for the given data.
     */
    function generateHash(string $data): string;
}