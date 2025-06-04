<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthenticationResponse;
use App\Contracts\User\UserLoginRequest;
use App\Contracts\User\UserRegisterRequest;
use App\Dtos\Users\UserDto;
use App\Entities\User\UserEntity;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\Enums\ClaimKey;
use App\Services\Session\SessionServiceInterface;
use App\Services\Token\TokenHandlerInterface;
use App\Validation\User\EmailValidator;
use App\Validation\User\PasswordValidator;
use App\Validation\User\UsernameValidator;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\InternalServerError;
use Core\Contracts\Api\Unauthorized;
use DateTime;

/**
 * @inheritdoc
 */
readonly class AuthService implements AuthServiceInterface
{
    function __construct(
        private UserRepositoryInterface $userRepository,
        private SessionRepositoryInterface $sessionRepository,
        private SessionServiceInterface $sessionService,
        private CryptographicServiceInterface $cryptographicService,
        private TokenHandlerInterface $tokenHandler
    )
    {
    }

    /**
     * @inheritdoc
     */
    function registerUser(UserRegisterRequest $request): AuthenticationResponse | ApiResponse
    {
        $username = $request->username;
        if (!UsernameValidator::validate($username))
        {
            return new BadRequest(
                "Username must be 4–16 characters long and can only contain letters, numbers, and underscores");
        }

        $email = $request->email;
        if (!EmailValidator::validate($email))
        {
            return new BadRequest("Email address must be valid with an active domain");
        }

        $password = $request->password;
        if (!PasswordValidator::validate($password))
        {
            return new BadRequest(
                "Password must be at least 8 characters long and include uppercase and lowercase letters, a number, and a special character");
        }

        if ($this->userRepository->findByEmail($email))
        {
            return new BadRequest("The email you have provided is already associated with an account");
        }

        $id = $this->cryptographicService->generateUniqueId();
        if (!$id)
        {
            return new InternalServerError("Unexpected server error");
        }

        $hash = $this->cryptographicService->generatePasswordHash($password);
        $userEntity = new UserEntity(
            $id,
            $username,
            $email,
            $hash,
            (new DateTime())
                ->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->userRepository->tryAdd($userEntity))
        {
            return new InternalServerError("Unexpected server error");
        }

        $userDto = new UserDto(
            $userEntity->username,
            $userEntity->email
        );
        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        return new AuthenticationResponse(
            $userDto,
            $sessionToken->accessToken
        );
    }

    /**
     * @inheritdoc
     */
    function loginUser(UserLoginRequest $request): AuthenticationResponse | ApiResponse
    {
        $userEntity = $this->userRepository->findByEmail($request->email);
        if ($userEntity == null)
        {
            return new BadRequest("Incorrect credentials");
        }

        if (!$this->cryptographicService->verifyPassword($userEntity->passwordHash, $request->password))
        {
            return new BadRequest("Incorrect credentials");
        }

        $userDto = new UserDto(
            $userEntity->username,
            $userEntity->email
        );
        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        return new AuthenticationResponse(
            $userDto,
            $sessionToken->accessToken
        );
    }

    /**
     * @inheritdoc
     */
    function validate(string $accessToken): AuthenticationResponse | ApiResponse
    {
        $payload = $this->tokenHandler->verifyAccessToken($accessToken);
        if (!$payload)
        {
            return new Unauthorized("Invalid access token");
        }

        $userEntity = $this->userRepository->findById($payload->getClaim(ClaimKey::Subject));
        if ($userEntity == null)
        {
            return new InternalServerError("Could not find user by claim");
        }

        $sessionEntity = $this->sessionRepository->findById($payload->getClaim(ClaimKey::SessionId));
        if ($sessionEntity == null)
        {
            return new InternalServerError("Could not find session by claim");
        }

        $userDto = new UserDto(
            $userEntity->username,
            $userEntity->email,
        );

        return new AuthenticationResponse(
            $userDto,
            $accessToken
        );
    }
}