<?php

namespace App\Services\Token;

use App\Entities\Session\SessionEntity;
use App\Services\Session\Domain\Payload;
use App\Services\Session\Domain\SessionToken;

/**
 * Represents the handler that is used to generate and verify session tokens.
 */
interface TokenHandlerInterface
{
    /**
     * Generates a session token for the given session entity.
     */
    function generateSessionToken(SessionEntity $sessionEntity): SessionToken;
    /**
     * Returns the payload for the access token if it verifies successfully, otherwise returns false.
     */
    function verifyAccessToken(string $accessToken): Payload | false;
}