<?php

namespace App\Services\Session;

use App\Entities\Session\SessionEntity;
use App\Entities\User\UserEntity;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Token\Payload;
use App\Services\Token\SessionToken;
use App\Services\Token\TokenServiceInterface;
use Core\Context\HttpContext;
use Core\Contracts\Api\ServerErrorResponse;
use DateInterval;
use DateTime;
use Exception;

readonly class SessionService implements SessionServiceInterface
{
    function __construct(
        private SessionRepositoryInterface $sessionRepository,
        private CryptographicServiceInterface $cryptographicService,
        private TokenServiceInterface $tokenService,
        private HttpContext $httpContext
    )
    {
    }

    function createSession(UserEntity $userEntity): SessionToken | ServerErrorResponse
    {
        // TODO: Check if session exists
        $authorizationToken = $this->httpContext->authorizationToken;

        $id = $this->cryptographicService->generateUniqueId();
        if (!$id)
        {
            return new ServerErrorResponse("Could not generate session ID");
        }

        $sessionEntity = new SessionEntity(
            $id,
            $userEntity->id,
            $this->httpContext->requestUserAgent,
            (new DateTime())->format(DATE_ISO8601_EXPANDED),
            (new DateTime())->add(new DateInterval("P1W"))->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->sessionRepository->tryAdd($sessionEntity))
        {
            return new ServerErrorResponse("Could not create session for user $userEntity->id");
        }

        return $this->tokenService->generateSessionToken($sessionEntity);
    }

    /**
     * @throws Exception
     */
    function validateAccessToken(string $accessToken): bool
    {
        $sessionToken = SessionToken::fromToken($accessToken);
        $decrypted = $this->cryptographicService->decrypt($sessionToken->payload);
        if (!$decrypted)
        {
            return false;
        }

        $json = json_decode($decrypted, true);
        $payload = Payload::fromArray($json);

        $sessionId = $payload->sessionId;
        $sessionEntity = $this->sessionRepository->findById($sessionId);
        if (!$sessionEntity)
        {
            return false;
        }

        $signature = base64_decode($sessionToken->signature);
        $recreatedSignature = $this->cryptographicService->generateHash($sessionToken->payload, true);

        if (!hash_equals($signature, $recreatedSignature))
        {
            return false;
        }

        $createdAt = new DateTime($sessionEntity->createdAt);
        $expiresAt = new DateTime($sessionEntity->expiresAt);

        if ($createdAt->getTimestamp() > $expiresAt->getTimestamp())
        {
            echo "Expired";
            return false;
        }

        // TODO: Remove session from DB

        return true;
    }
}