<?php

namespace App\Services\Cryptographic;

use Core\Enums\EnvironmentKey;
use Core\Environment\Environment;
use Random\RandomException;

/**
 * @inheritdoc
 */
readonly class CryptographicService implements CryptographicServiceInterface
{
    /**
     * Default password hashing algorithm.
     */
    private const PASSWORD_ALGORITHM = PASSWORD_ARGON2ID;
    /**
     * Default hashing algorithm.
     */
    public const HASH_ALGORITHM = "sha3-512";
    /**
     * Default file hashing algorithm.
     */
    public const FILE_HASH_ALGORITHM = "sha256";
    /**
     * Default encryption algorithm.
     */
    private const ENCRYPTION_ALGORITHM = "aes-256-cbc";

    function __construct(private Environment $environment)
    {
    }

    /**
     * @inheritdoc
     */
    function generateUuid(): string | false
    {
        try
        {
            $data = random_bytes(16);
            $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
            $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        } catch (RandomException)
        {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    function encrypt(string $data): string
    {
        $privateKey = base64_encode($this->environment->get(EnvironmentKey::PRIVATE_ENCRYPTION_KEY));

        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_ALGORITHM);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($data, self::ENCRYPTION_ALGORITHM, $privateKey, OPENSSL_RAW_DATA, $iv);
        $hash = $this->sign($encrypted, self::HASH_ALGORITHM, true);

        return base64_encode($iv . $hash . $encrypted);
    }

    /**
     * @inheritdoc
     */
    function decrypt(string $input): string | false
    {
        $privateKey = base64_encode($this->environment->get(EnvironmentKey::PRIVATE_ENCRYPTION_KEY));
        $decoded = base64_decode($input);

        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_ALGORITHM);
        $iv = substr($decoded, 0, $ivLength);
        $hash = substr($decoded, $ivLength, 64);
        $encrypted = substr($decoded, $ivLength + 64);

        $data = openssl_decrypt($encrypted, self::ENCRYPTION_ALGORITHM, $privateKey, OPENSSL_RAW_DATA, $iv);
        $recreatedHash = $this->sign($encrypted, self::HASH_ALGORITHM, true);

        if (hash_equals($hash, $recreatedHash))
        {
            return $data;
        }

        return false;
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
    function sign(string $data, string $algorithm, bool $binary = false): string
    {
        return hash_hmac($algorithm, $data, $this->environment->get(EnvironmentKey::HASH_KEY), $binary);
    }

    /**
     * @inheritdoc
     */
    function signFile(string $file): string
    {
        return hash_file(self::FILE_HASH_ALGORITHM, $file);
    }
}