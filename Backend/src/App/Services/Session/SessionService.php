<?php

namespace App\Services\Session;

use App\Entities\Session\SessionEntity;
use App\Entities\User\UserEntity;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Domain\SessionToken;
use Core\Configuration\Configuration;
use Core\Context\HttpContext;
use Core\Contracts\Api\InternalServerError;
use DateInterval;
use DateTime;
use Exception;

readonly class SessionService implements SessionServiceInterface
{
    function __construct(
        private SessionRepositoryInterface $sessionRepository,
        private CryptographicServiceInterface $cryptographicService,
        private HttpContext $httpContext,
        private Configuration $configuration
    )
    {
    }

    /**
     * @throws Exception
     */
    function createSession(UserEntity $userEntity): SessionToken | InternalServerError
    {
        $id = $this->cryptographicService->generateUniqueId();
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

        return $this->cryptographicService->generateSessionToken($sessionEntity);
    }
}