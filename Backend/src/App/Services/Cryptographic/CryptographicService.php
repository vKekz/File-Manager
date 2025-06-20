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
     * Default hashing algorithm.
     */
    public const HASH_ALGORITHM = "sha512";
    /**
     * Default password hashing algorithm.
     */
    private const PASSWORD_ALGORITHM = PASSWORD_ARGON2ID;
    /**
     * Default encryption algorithm.
     */
    private const ENCRYPTION_ALGORITHM = "aes-256-gcm";

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
    function generateKey(int $size = 32): string | false
    {
        try
        {
            return base64_encode(random_bytes($size));
        } catch (RandomException)
        {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    function encrypt(string $data, ?string $key = null): string
    {
        // Get default master key if none is supplied
        if ($key === null)
        {
            $key = $this->environment->get(EnvironmentKey::ENCRYPTION_MASTER_KEY);
        }

        $keyHash = openssl_digest($key, self::HASH_ALGORITHM, true);
        $vectorLength = openssl_cipher_iv_length(self::ENCRYPTION_ALGORITHM);
        $vector = openssl_random_pseudo_bytes($vectorLength);

        $encrypted = openssl_encrypt(
            $data,
            self::ENCRYPTION_ALGORITHM,
            $keyHash,
            OPENSSL_RAW_DATA,
            $vector,
            $tag
        );
        $hash = $this->sign($encrypted, self::HASH_ALGORITHM, true);

        return base64_encode($vector . $tag . $hash . $encrypted);
    }

    /**
     * @inheritdoc
     */
    function decrypt(string $input, ?string $key = null): string | false
    {
        // Get default master key if none is supplied
        if ($key === null)
        {
            $key = $this->environment->get(EnvironmentKey::ENCRYPTION_MASTER_KEY);
        }

        $vectorLength = openssl_cipher_iv_length(self::ENCRYPTION_ALGORITHM);
        $hashLength = 64;
        $tagLength = 16;

        $inputRaw = base64_decode($input);
        $hash = substr($inputRaw, $vectorLength + $tagLength, $hashLength);
        $raw = substr($inputRaw, $vectorLength + $tagLength + $hashLength);
        $recreatedHash = $this->sign($raw, self::HASH_ALGORITHM, true);

        // Only return data if signatures match
        if (hash_equals($hash, $recreatedHash))
        {
            $keyHash = openssl_digest($key, self::HASH_ALGORITHM, true);
            $vector = substr($inputRaw, 0, $vectorLength);
            $tag = substr($inputRaw, $vectorLength, $tagLength);

            return openssl_decrypt(
                $raw,
                self::ENCRYPTION_ALGORITHM,
                $keyHash,
                OPENSSL_RAW_DATA,
                $vector,
                $tag
            );
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
        return hash_hmac($algorithm, $data, $this->environment->get(EnvironmentKey::HASH_MASTER_KEY), $binary);
    }

    /**
     * @inheritdoc
     */
    function signFile(string $file, string $algorithm = "sha256"): string
    {
        return hash_file($algorithm, $file);
    }
}