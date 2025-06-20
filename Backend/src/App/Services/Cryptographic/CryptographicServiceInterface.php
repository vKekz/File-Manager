<?php

namespace App\Services\Cryptographic;

/**
 * Represents the service that is used for cryptographically secure functions.
 */
interface CryptographicServiceInterface
{
    /**
     * Attempts to generate a UUID v4, otherwise returns false on failure.
     *
     * Solution was found on stackoverflow: https://stackoverflow.com/a/15875555
     */
    function generateUuid(): string | false;
    /**
     * Attempts to generate a random key of given byte size as Base64, otherwise returns false on failure.
     */
    function generateKey(int $size = 32): string | false;
    /**
     * Encrypts the given data using the cryptographic standard "AES".
     *
     * @see https://www.php.net/manual/en/function.openssl-encrypt.php
     */
    function encrypt(string $data, ?string $key = null): string;
    /**
     * Decrypts the given input, otherwise returns false on failure.
     *
     * @see https://www.php.net/manual/en/function.openssl-decrypt.php
     */
    function decrypt(string $input, ?string $key = null): string | false;
    /**
     * Generates a password hash for the given input.
     */
    function generatePasswordHash(string $input): string;
    /**
     * Verifies that the given user password matches its hash.
     */
    function verifyPassword(string $hash, string $userPassword): bool;
    /**
     * Signs the given data using a secret key.
     */
    function sign(string $data, string $algorithm, bool $binary = false): string;
    /**
     * Returns a hash signature for the content of the given file.
     */
    function signFile(string $file, string $algorithm = "sha256"): string;
}