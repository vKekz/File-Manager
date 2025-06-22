<?php

namespace App\Services\User;

use App\Contracts\User\UpdateUserSettingsRequest;
use App\Entities\User\UserSettings;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Session\Enums\ClaimKey;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;

readonly class UserService implements UserServiceInterface
{
    function __construct(
        private UserRepositoryInterface $userRepository,
        private SessionRepositoryInterface $sessionRepository,
        private HttpContext $httpContext)
    {
    }

    function changeSettings(UpdateUserSettingsRequest $request): UserSettings | ApiResponse
    {
        $user = $this->httpContext->user;
        if ($user->settings->equals($request->settings))
        {
            return new BadRequest("Submitted settings match current settings");
        }

        if ($user->id !== $request->id)
        {
            return new BadRequest("Cannot change the settings of another user");
        }

        $condition = "WHERE Id = ?";
        $this->userRepository
             ->tryUpdate(["Settings"], [json_encode($request->settings), $request->id], $condition);

        return $request->settings;
    }

    function logout(): void
    {
        $payload = $this->httpContext->rawPayload;
        $sessionId = $payload->getClaim(ClaimKey::SessionId);

        $this->sessionRepository->tryRemove($sessionId);
    }
}