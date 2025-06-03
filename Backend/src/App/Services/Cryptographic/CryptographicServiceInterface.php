<?php

namespace App\Services\Cryptographic;

/**
 * Represents the service that is used for cryptographically secure functions.
 */
interface CryptographicServiceInterface
{
    /**
     * Attempts to generate a unique number ID, otherwise returns false on failure.
     */
    function generateUniqueId(): int | false;
    /**
     * Encrypts the given data using the cryptographic standard "AES".
     */
    function encrypt(string $data): string;
    /**
     * Decrypts the given input, otherwise returns false on failure.
     */
    function decrypt(string $input): string | false;
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