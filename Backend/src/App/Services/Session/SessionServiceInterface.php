<?php

namespace App\Services\Session;

use App\Entities\User\UserEntity;
use App\Services\Session\Token\SessionToken;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\InternalServerError;

/**
 * Represents the service that is used for the creation of user sessions.
 */
interface SessionServiceInterface
{
    /**
     * Creates a new session token for the given user.
     */
    function createSession(UserEntity $userEntity): SessionToken | InternalServerError;
    /**
     * Validates the given access token against the real session.
     */
    function validateSession(?string $accessToken): true | ApiResponse;
}