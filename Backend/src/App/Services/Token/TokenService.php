<?php

namespace App\Services\Token;

use App\Entities\Session\SessionEntity;
use App\Services\Cryptographic\CryptographicServiceInterface;

readonly class TokenService implements TokenServiceInterface
{
    function __construct(private CryptographicServiceInterface $cryptographicService)
    {
    }

    function generateCsrfToken(): string
    {
        return "";
    }

    function generateSessionToken(SessionEntity $sessionEntity): SessionToken
    {
        $payload = new Payload(
            $sessionEntity->id,
            $sessionEntity->userId,
            $sessionEntity->deviceData,
            $sessionEntity->createdAt
        );
        $json = json_encode($payload);
        $encrypted = $this->cryptographicService->encrypt($json);
        $signature = base64_encode($this->cryptographicService->generateHash($encrypted, true));

        return new SessionToken($encrypted, $signature);
    }
}