<?php

namespace App\Services\User;

use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use App\Repositories\User\UserRepositoryInterface;

/**
 * @inheritdoc
 */
readonly class UserService implements UserServiceInterface
{
    function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function getUsers(): array
    {
        return $this->userRepository->getUsers();
    }

    function getUserById(string $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }

    function registerUser(RegisterUserRequest $request): RegisterUserResponse
    {
        $id = random_int(0, PHP_INT_MAX);
        $passwordHash = password_hash($request->password, PASSWORD_ARGON2ID);
        $userEntity = new UserEntity($id, $request->userName, $request->email, $passwordHash);

        $this->userRepository->trySave($userEntity);

        return new RegisterUserResponse($id);
    }
}