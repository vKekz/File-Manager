<?php

namespace App\Services\User;

use App\Contracts\Session\SessionResponse;
use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Dtos\Users\UserDto;
use App\Entities\User\UserEntity;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\SessionServiceInterface;
use App\Validation\User\EmailValidator;
use App\Validation\User\PasswordValidator;
use App\Validation\User\UsernameValidator;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequestResponse;
use Core\Contracts\Api\ServerErrorResponse;
use DateTime;

/**
 * @inheritdoc
 */
readonly class UserService implements UserServiceInterface
{
    function __construct(
        private UserRepositoryInterface $userRepository,
        private SessionServiceInterface $sessionService,
        private CryptographicServiceInterface $cryptographicService
    )
    {
    }

    /**
     * @inheritdoc
     */
    function getUsers(): array
    {
        return $this->userRepository->getUsers();
    }

    /**
     * @inheritdoc
     */
    function getUserById(int $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @inheritdoc
     */
    function registerUser(RegisterUserRequest $request): SessionResponse | ApiResponse
    {
        $username = $request->username;
        if (!UsernameValidator::validate($username))
        {
            return new BadRequestResponse(
                "Username must be 4–16 characters long and can only contain letters, numbers, and underscores");
        }

        $email = $request->email;
        if (!EmailValidator::validate($email))
        {
            return new BadRequestResponse("Email address must be valid with an active domain");
        }

        $password = $request->password;
        if (!PasswordValidator::validate($password))
        {
            return new BadRequestResponse(
                "Password must be at least 8 characters long and include uppercase and lowercase letters, a number, and a special character");
        }

        if ($this->userRepository->findByEmail($email))
        {
            return new BadRequestResponse("The email you have provided is already associated with an account");
        }

        $id = $this->cryptographicService->generateUniqueId();
        if (!$id)
        {
            return new ServerErrorResponse("Unexpected server error");
        }

        $hash = $this->cryptographicService->generatePasswordHash($password);
        $userEntity = new UserEntity(
            $id,
            $username,
            $email,
            $hash,
            (new DateTime())->format(DATE_ISO8601_EXPANDED)
        );

        if (!$this->userRepository->tryAdd($userEntity))
        {
            return new ServerErrorResponse("Unexpected server error");
        }

        $userDto = new UserDto(
            $userEntity->id,
            $userEntity->username,
            $userEntity->email
        );
        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        return new SessionResponse(
            $userDto,
            $sessionToken->toString()
        );
    }

    /**
     * @inheritdoc
     */
    function loginUser(LoginUserRequest $request): SessionResponse | ApiResponse
    {
        $userEntity = $this->userRepository->findByEmail($request->email);
        if ($userEntity == null)
        {
            return new BadRequestResponse("Incorrect credentials");
        }

        if (!$this->cryptographicService->verifyPassword($userEntity->passwordHash, $request->password))
        {
            return new BadRequestResponse("Incorrect credentials");
        }

        $userDto = new UserDto(
            $userEntity->id,
            $userEntity->username,
            $userEntity->email
        );
        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        return new SessionResponse(
            $userDto,
            $sessionToken->toString()
        );
    }

    /**
     * @inheritdoc
     */
    function logoutUser()
    {
        // TODO: Implement logoutUser() method.
    }

    /**
     * @inheritdoc
     */
    function deleteUser(int $id): bool
    {
        return $this->userRepository->tryRemove($id);
    }
}