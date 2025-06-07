<?php

namespace App\Services\Token;

use App\Entities\Session\SessionEntity;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Enums\ClaimKey;
use App\Services\Session\Token\Header;
use App\Services\Session\Token\Payload;
use App\Services\Session\Token\SessionToken;
use DateTime;
use Exception;

/**
 * @inheritdoc
 */
readonly class TokenHandler implements TokenHandlerInterface
{
    function __construct(private CryptographicServiceInterface $cryptographicService)
    {
    }

    /**
     * @inheritdoc
     */
    function generateSessionToken(SessionEntity $sessionEntity): SessionToken
    {
        $algorithm = CryptographicService::HASH_ALGORITHM;
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
        $signatureBase64 = base64_encode($this->cryptographicService->sign($data, $algorithm, true));

        return new SessionToken(
            "$headerBase64.$payloadBase64.$signatureBase64"
        );
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    function verifyAccessToken(?string $accessToken): Payload | false
    {
        if ($accessToken === null || strlen($accessToken) === 0)
        {
            return false;
        }

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
        $recreatedSignature = $this->cryptographicService->sign("$headerBase64.$payloadBase64", $header->alg, true);
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