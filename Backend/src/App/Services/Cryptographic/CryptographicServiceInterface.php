<?php

namespace App\Services\Cryptographic;

use App\Entities\Session\SessionEntity;
use App\Services\Session\Domain\Payload;
use App\Services\Session\Domain\SessionToken;

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
     * Signs the given data using a secret key.
     */
    function sign(string $data, string $algorithm = "sha256", bool $binary = false): string;
    /**
     * Generates a session token for the given session entity.
     */
    function generateSessionToken(SessionEntity $sessionEntity): SessionToken;
    /**
     * Returns the payload for the access token if it verifies successfully, otherwise returns false.
     */
    function verifyAccessToken(string $accessToken): Payload | false;
}