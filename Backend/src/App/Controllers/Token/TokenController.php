<?php

namespace App\Controllers\Token;

use App\Services\Session\SessionServiceInterface;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\HeaderParameter;
use Core\Authorization\AuthorizationToken;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\OkResponse;
use Core\Controllers\ApiController;

class TokenController extends ApiController
{
    private const END_POINT = "api/token";

    function __construct(private readonly SessionServiceInterface $sessionService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpPost("/validate")]
    public function validateToken(#[HeaderParameter("Authorization")] string $accessToken): ApiResponse
    {
        $authorizationToken = AuthorizationToken::fromHeader($accessToken);
        return new OkResponse(
            $this->sessionService->validateAccessToken($authorizationToken->token)
        );
    }
}