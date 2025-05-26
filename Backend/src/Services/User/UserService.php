<?php

namespace Services\User;

use Contracts\User\RegisterUserRequest;
use Contracts\User\RegisterUserResponse;
use Database\Repositories\User\UserRepositoryInterface;
use Entities\User\UserEntity;

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