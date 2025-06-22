<?php

namespace App\Services\Auth;

use App\Contracts\Auth\AuthenticationResponse;
use App\Contracts\User\UserLoginRequest;
use App\Contracts\User\UserRegisterRequest;
use App\Dtos\Users\UserDto;
use App\Entities\User\UserEntity;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\FileSystem\FileSystemHandlerInterface;
use App\Services\Session\SessionServiceInterface;
use App\Validation\User\EmailValidator;
use App\Validation\User\PasswordValidator;
use App\Validation\User\UsernameValidator;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Contracts\Api\InternalServerError;
use DateTime;

/**
 * @inheritdoc
 */
readonly class AuthService implements AuthServiceInterface
{
    function __construct(
        private UserRepositoryInterface $userRepository,
        private DirectoryRepositoryInterface $directoryRepository,
        private SessionServiceInterface $sessionService,
        private CryptographicServiceInterface $cryptographicService,
        private FileSystemHandlerInterface $fileSystemHandler
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

        $emailHash = $this->cryptographicService->sign($email, CryptographicService::HASH_ALGORITHM);
        if ($this->userRepository->findByEmailHash($emailHash))
        {
            return new BadRequest("The email you have provided is already associated with an account");
        }

        $id = $this->cryptographicService->generateUuid();
        if (!$id)
        {
            return new InternalServerError();
        }

        $key = $this->cryptographicService->generateKey();
        if (!$key)
        {
            return new InternalServerError();
        }

        $hash = $this->cryptographicService->generatePasswordHash($password);
        $encryptedHash = $this->cryptographicService->encrypt($hash, $key);
        $encryptedKey = $this->cryptographicService->encrypt($key);

        $encryptedEmail = $this->cryptographicService->encrypt($email, $key);
        $encryptedUserName = $this->cryptographicService->encrypt($username, $key);

        $userEntity = new UserEntity(
            $id,
            $encryptedUserName,
            $encryptedEmail,
            $emailHash,
            $encryptedHash,
            $encryptedKey,
            (new DateTime())
                ->format(DATE_RFC3339)
        );

        if (!$this->userRepository->tryAdd($userEntity))
        {
            return new InternalServerError();
        }

        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        // Make sure to create default root directory for user
        $this->directoryRepository->createRootDirectoryForUser($id);

        // Create root folder on file system
        $this->fileSystemHandler->createDirectory($this->fileSystemHandler->getAbsolutePath($id));

        return new AuthenticationResponse(
            new UserDto(
                $userEntity->id,
                $username,
                $email,
                $userEntity->settings
            ),
            $sessionToken->accessToken
        );
    }

    /**
     * @inheritdoc
     */
    function loginUser(UserLoginRequest $request): AuthenticationResponse | ApiResponse
    {
        $emailHash = $this->cryptographicService->sign($request->email, CryptographicService::HASH_ALGORITHM);
        $userEntity = $this->userRepository->findByEmailHash($emailHash);
        if ($userEntity == null)
        {
            // TODO: Not safe to timing attack
            return new BadRequest("Incorrect credentials");
        }

        $key = $this->cryptographicService->decrypt($userEntity->privateKey);
        $decryptedPasswordHash = $this->cryptographicService->decrypt($userEntity->passwordHash, $key);
        if (!$this->cryptographicService->verifyPassword($decryptedPasswordHash, $request->password))
        {
            return new BadRequest("Incorrect credentials");
        }

        $sessionToken = $this->sessionService->createSession($userEntity);
        if ($sessionToken instanceof ApiResponse)
        {
            return $sessionToken;
        }

        $decryptedEmail = $this->cryptographicService->decrypt($userEntity->email, $key);
        $decryptedUserName = $this->cryptographicService->decrypt($userEntity->username, $key);

        return new AuthenticationResponse(
            new UserDto(
                $userEntity->id,
                $decryptedUserName,
                $decryptedEmail,
                $userEntity->settings
            ),
            $sessionToken->accessToken
        );
    }
}