<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthenticationResponse;
use App\Contracts\User\UserLoginRequest;
use App\Contracts\User\UserRegisterRequest;
use App\Services\Session\Token\Payload;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for user authentication.
 */
interface AuthServiceInterface
{
    /**
     * Registers a new user.
     */
    function registerUser(UserRegisterRequest $request): AuthenticationResponse | ApiResponse;
    /**
     * Login a user.
     */
    function loginUser(UserLoginRequest $request): AuthenticationResponse | ApiResponse;
    /**
     * Validates the given access token against the real session and returns an authenticated user response.
     */
    function validateSession(?string $accessToken): AuthenticationResponse | ApiResponse;
    /**
     * Validates the access token from the current authorization header and returns the payload or false.
     */
    function validateAuthHeader(): Payload | false;
}