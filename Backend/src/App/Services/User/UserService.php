<?php

namespace App\Services\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Hash\HashServiceInterface;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequestResponse;
use Core\Contracts\Api\ServerErrorResponse;
use Random\RandomException;

/**
 * @inheritdoc
 */
readonly class UserService implements UserServiceInterface
{
    function __construct(private UserRepositoryInterface $userRepository, private HashServiceInterface $hashService)
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
    function registerUser(RegisterUserRequest $request): RegisterUserResponse | ApiResponse
    {
        if ($this->userRepository->findByEmail($request->email))
        {
            return new BadRequestResponse("Email has been used already");
        }

        try
        {
            $id = random_int(0, PHP_INT_MAX);
        } catch (RandomException)
        {
            return new ServerErrorResponse("Could not generate ID");
        }

        $hash = $this->hashService->generatePasswordHash($request->password);
        $userEntity = new UserEntity($id, $request->username, $request->email, $hash);

        $this->userRepository->tryAdd($userEntity);

        // TODO: Session
        return new RegisterUserResponse($id);
    }

    /**
     * @inheritdoc
     */
    function loginUser(LoginUserRequest $request)
    {
        $foundUserEntity = $this->userRepository->findByEmail($request->email);
        if ($foundUserEntity == null)
        {
            return new BadRequestResponse();
        }

        if (!$this->hashService->verifyPassword($foundUserEntity->passwordHash, $request->password))
        {
            return new BadRequestResponse();
        }

        // TODO: Session
        return $foundUserEntity->id;
    }

    /**
     * @inheritdoc
     */
    function deleteUser(int $id): bool
    {
        return $this->userRepository->tryRemove($id);
    }
}