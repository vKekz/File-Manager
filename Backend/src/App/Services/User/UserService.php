<?php

namespace App\Services\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use App\Repositories\User\UserRepositoryInterface;
use Core\Contracts\Api\ServerErrorResponse;
use Random\RandomException;

/**
 * @inheritdoc
 */
readonly class UserService implements UserServiceInterface
{
    function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @inheritdoc
     */
    public function getUsers(): array
    {
        return $this->userRepository->getUsers();
    }

    /**
     * @inheritdoc
     */
    function getUserById(string $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @inheritdoc
     */
    function registerUser(RegisterUserRequest $request): RegisterUserResponse | ServerErrorResponse
    {
        if ($this->isEmailRegistered($request->email))
        {
            return new ServerErrorResponse("Email already registered");
        }

        try
        {
            $id = random_int(0, PHP_INT_MAX);
        } catch (RandomException)
        {
            return new ServerErrorResponse("Could not generate ID");
        }

        // TODO: Move to separate service
        // TODO: Session
        $passwordHash = password_hash($request->password, PASSWORD_ARGON2ID);
        $userEntity = new UserEntity($id, $request->username, $request->email, $passwordHash);

        $this->userRepository->tryAdd($userEntity);

        return new RegisterUserResponse($id);
    }

    /**
     * @inheritdoc
     */
    function loginUser(LoginUserRequest $request)
    {
        // TODO: Check if user exists
        // TODO: Verify password
        // TODO: Session
    }

    /**
     * @inheritdoc
     */
    function deleteUser(string $id): bool
    {
        return $this->userRepository->tryRemove($id);
    }

    private function isEmailRegistered(string $email): bool
    {
        foreach ($this->userRepository->getUsers() as $userEntity)
        {
            if (strcmp($userEntity->email, $email) == 0)
            {
                return true;
            }
        }

        return false;
    }
}