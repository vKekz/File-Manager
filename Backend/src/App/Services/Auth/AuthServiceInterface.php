<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthenticationResponse;
use App\Contracts\User\UserLoginRequest;
use App\Contracts\User\UserRegisterRequest;
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
}