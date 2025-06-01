<?php

namespace App\Services\Session;

use App\Entities\Session\SessionEntity;
use App\Entities\User\UserEntity;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Services\Hash\HashServiceInterface;
use Core\Context\HttpContext;
use Core\Contracts\Api\ServerErrorResponse;

readonly class SessionService implements SessionServiceInterface
{
    function __construct(private SessionRepositoryInterface $sessionRepository, private HttpContext $httpContext, private HashServiceInterface $hashService)
    {
    }

    // TODO: Validate session

    function createSession(UserEntity $userEntity): SessionEntity | ServerErrorResponse
    {
        $id = $this->hashService->generateUniqueId();
        if (!$id)
        {
            return new ServerErrorResponse("Could not generate session ID");
        }

        // TODO: Check if session exists
        if (!session_start())
        {
            return new ServerErrorResponse("Could not start session");
        }

        $sessionId = session_id();
        if (!$sessionId)
        {
            return new ServerErrorResponse("Could not retrieve session ID");
        }

        $data = $userEntity->id . $this->httpContext->requestAddress . $this->httpContext->requestUserAgent;
        $hash = $this->hashService->generateHash($data);
        $sessionEntity = new SessionEntity($id, $userEntity->id, $sessionId, $hash);

        $this->sessionRepository->tryAdd($sessionEntity);

        return $sessionEntity;
    }
}