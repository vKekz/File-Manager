<?php

namespace App\Controllers\Session;

use App\Contracts\Auth\AuthenticationResponse;
use Core\Attributes\Authorization\Authorize;
use Core\Attributes\Http\HttpPost;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Contracts\Api\Unauthorized;
use Core\Controllers\ApiController;

/**
 * Represents the controller that is used for user session management.
 */
#[Authorize]
class SessionController extends ApiController
{
    private const END_POINT = "api/session";

    function __construct(private readonly HttpContext $httpContext)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpPost("/validate")]
    function validateSession(): ApiResponse
    {
        $user = $this->httpContext->user;
        $token = $this->httpContext->authorizationToken;

        if (!$user || !$token)
        {
            return new Unauthorized("Invalid user");
        }

        return new Ok(
            new AuthenticationResponse(
                $this->httpContext->user,
                $this->httpContext->authorizationToken->token
            )
        );
    }
}