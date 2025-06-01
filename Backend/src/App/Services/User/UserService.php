<?php

namespace App\Services\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Contracts\User\RegisterUserResponse;
use App\Entities\User\UserEntity;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Hash\HashServiceInterface;
use App\Services\Session\SessionServiceInterface;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequestResponse;
use Core\Contracts\Api\ServerErrorResponse;

/**
 * @inheritdoc
 */
readonly class UserService implements UserServiceInterface
{
    function __construct(
        private UserRepositoryInterface $userRepository,
        private SessionServiceInterface $sessionService,
        private HashServiceInterface $hashService)
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
        // TODO: Input validation

        if ($this->userRepository->findByEmail($request->email))
        {
            return new BadRequestResponse("The email you have provided is already associated with an account");
        }

        $id = $this->hashService->generateUniqueId();
        if (!$id)
        {
            return new ServerErrorResponse("Could not generate User ID");
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
            return new BadRequestResponse("Incorrect credentials");
        }

        if (!$this->hashService->verifyPassword($foundUserEntity->passwordHash, $request->password))
        {
            return new BadRequestResponse("Incorrect credentials");
        }

        var_dump($this->sessionService->createSession($foundUserEntity));

        // TODO: Session
        return $foundUserEntity->id;
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