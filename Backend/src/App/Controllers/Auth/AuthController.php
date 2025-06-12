<?php

namespace App\Controllers\Auth;

use App\Contracts\User\UserLoginRequest;
use App\Contracts\User\UserRegisterRequest;
use App\Services\Auth\AuthServiceInterface;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\BodyParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Controllers\ApiController;

/**
 * Represents the controller that is used for the user authentication.
 */
class AuthController extends ApiController
{
    private const END_POINT = "api/auth";

    function __construct(private readonly AuthServiceInterface $authService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpPost("/register")]
    function registerUser(#[BodyParameter] string $body): ApiResponse
    {
        $request = UserRegisterRequest::deserialize($body);
        $response = $this->authService->registerUser($request);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }

    #[HttpPost("/login")]
    function loginUser(#[BodyParameter] string $body): ApiResponse
    {
        $request = UserLoginRequest::deserialize($body);
        $response = $this->authService->loginUser($request);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }
}