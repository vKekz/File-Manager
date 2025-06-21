<?php

namespace App\Services\Session;

use App\Entities\Session\SessionEntity;
use App\Entities\User\UserEntity;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Enums\ClaimKey;
use App\Services\Session\Token\SessionToken;
use App\Services\Token\TokenHandlerInterface;
use Core\Configuration\Configuration;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\InternalServerError;
use Core\Contracts\Api\Unauthorized;
use DateInterval;
use DateTime;
use Exception;

/**
 * @inheritdoc
 */
readonly class SessionService implements SessionServiceInterface
{
    function __construct(
        private SessionRepositoryInterface $sessionRepository,
        private UserRepositoryInterface $userRepository,
        private CryptographicServiceInterface $cryptographicService,
        private TokenHandlerInterface $tokenHandler,
        private HttpContext $httpContext,
        private Configuration $configuration
    )
    {
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    function createSession(UserEntity $userEntity): SessionToken | InternalServerError
    {
        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError("Could not generate session ID");
        }

        $sessionEntity = new SessionEntity(
            $id,
            $userEntity->id,
            $this->httpContext->requestUserAgent,
            (new DateTime())
                ->format(DATE_ISO8601_EXPANDED),
            (new DateTime())
                ->add(new DateInterval($this->configuration->authenticationTokenLifetime))
                ->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->sessionRepository->tryAdd($sessionEntity))
        {
            return new InternalServerError("Could not create session for user $userEntity->id");
        }

        return $this->tokenHandler->generateSessionToken($sessionEntity);
    }

    /**
     * @inheritdoc
     */
    function validateSession(?string $accessToken): true | ApiResponse
    {
        $payload = $this->tokenHandler->verifyAccessToken($accessToken);
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        $userId = $payload->getClaim(ClaimKey::Subject);
        $userEntity = $this->userRepository->findById($userId);
        if ($userEntity == null)
        {
            return new InternalServerError("Could not find user by claim");
        }

        $sessionEntity = $this->sessionRepository->findById($payload->getClaim(ClaimKey::SessionId));
        if ($sessionEntity == null)
        {
            return new InternalServerError("Could not find session by claim");
        }

        if ($sessionEntity->userId !== $userId)
        {
            return new Unauthorized("Invalid access token");
        }

        // Get user from session and save in context for future use
        $key = $this->cryptographicService->decrypt($userEntity->privateKey);
        $decryptedEmail = $this->cryptographicService->decrypt($userEntity->email, $key);
        $decryptedUserName = $this->cryptographicService->decrypt($userEntity->username, $key);

        $userEntity->email = $decryptedEmail;
        $userEntity->username = $decryptedUserName;
        $userEntity->privateKey = $key;

        $this->httpContext->rawPayload = $payload;
        $this->httpContext->user = $userEntity;

        return true;
    }
}