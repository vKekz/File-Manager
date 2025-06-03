<?php

namespace App\Services\Cryptographic;

use App\Entities\Session\SessionEntity;
use App\Services\Session\Domain\Header;
use App\Services\Session\Domain\Payload;
use App\Services\Session\Domain\SessionToken;
use App\Services\Session\Enums\ClaimKey;
use Core\Enums\EnvironmentKey;
use Core\Environment\Environment;
use DateTime;
use Exception;
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
    private const HASH_ALGORITHM = "sha3-512";
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
     * @inheritDoc
     */
    function encrypt(string $data): string
    {
        $privateKey = base64_encode($this->environment->get(EnvironmentKey::PRIVATE_ENCRYPTION_KEY));

        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_ALGORITHM);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $encrypted = openssl_encrypt($data, self::ENCRYPTION_ALGORITHM, $privateKey, OPENSSL_RAW_DATA, $iv);
        $hash = $this->sign($encrypted, true);

        return base64_encode($iv . $hash . $encrypted);
    }

    /**
     * @inheritDoc
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
        $recreatedHash = $this->sign($encrypted, true);

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
    function sign(string $data, string $algorithm = self::HASH_ALGORITHM , bool $binary = false): string
    {
        return hash_hmac($algorithm, $data, $this->environment->get(EnvironmentKey::HASH_KEY), $binary);
    }

    /**
     * @inheritdoc
     */
    function generateSessionToken(SessionEntity $sessionEntity): SessionToken
    {
        $algorithm = self::HASH_ALGORITHM;
        $header = new Header($algorithm);
        $payload = new Payload(
            [
                ClaimKey::Subject->value => $sessionEntity->userId,
                ClaimKey::SessionId->value => $sessionEntity->id,
                ClaimKey::IssuedAt->value => $sessionEntity->issuedAt,
                ClaimKey::ExpiresAt->value => $sessionEntity->expiresAt,
            ]
        );

        $headerBase64 = base64_encode(json_encode($header));
        $payloadBase64 = base64_encode(json_encode($payload));

        $data = "$headerBase64.$payloadBase64";
        $signatureBase64 = base64_encode($this->sign($data, $algorithm, true));

        return new SessionToken(
            "$headerBase64.$payloadBase64.$signatureBase64"
        );
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    function verifyAccessToken(string $accessToken): Payload | false
    {
        // Check for 3 parts split by "."
        $parts = explode(".", $accessToken, 3);
        if (count($parts) !== 3)
        {
            return false;
        }

        [$headerBase64, $payloadBase64, $signatureBase64] = $parts;

        $headerRaw = base64_decode($headerBase64, true);
        $payloadRaw = base64_decode($payloadBase64, true);
        $signatureRaw = base64_decode($signatureBase64, true);
        if (!$headerRaw || !$payloadRaw || !$signatureRaw)
        {
            return false;
        }

        // Check if algorithm is set in header
        $header = Header::fromArray(json_decode($headerRaw, true));
        if (!isset($header->alg))
        {
            return false;
        }

        // Verify signature
        $recreatedSignature = $this->sign("$headerBase64.$payloadBase64", $header->alg, true);
        if (!hash_equals($recreatedSignature, $signatureRaw))
        {
            return false;
        }

        // Check if claims are set in the payload
        $payloadJson = json_decode($payloadRaw, true);
        if (!isset($payloadJson["claims"]))
        {
            return false;
        }

        $payload = new Payload($payloadJson["claims"]);
        $issuedAtUnix = (new DateTime($payload->getClaim(ClaimKey::IssuedAt)))->getTimestamp();
        $currentUnix = time();

        // Check that the token has been created before current time
        if ($issuedAtUnix > $currentUnix)
        {
            return false;
        }

        // Check if the token has expired
        $expiresAtUnix = (new DateTime($payload->getClaim(ClaimKey::ExpiresAt)))->getTimestamp();
        if ($expiresAtUnix < $currentUnix)
        {
            return false;
        }

        return $payload;
    }
}