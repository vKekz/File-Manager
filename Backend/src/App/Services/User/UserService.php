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

    function registerUser(RegisterUserRequest $request): RegisterUserResponse
    {
        return new RegisterUserResponse($request->email);
    }

    function getUserById(string $id): ?UserEntity
    {
        return $this->userRepository->findById($id);
    }
}