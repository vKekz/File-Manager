<?php

namespace App\Services\Session\Token;

/**
 * Represents a customized token that is issued for user sessions.
 *
 * Implementation is based on the JWT spec.
 * @see https://tools.ietf.org/html/rfc7519
 */
readonly class SessionToken
{
    function __construct(public string $accessToken)
    {
    }
}