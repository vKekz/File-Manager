<?php

namespace App\Controllers\User;

use App\Contracts\User\LoginUserRequest;
use App\Contracts\User\RegisterUserRequest;
use App\Services\User\UserServiceInterface;
use Core\Attributes\Http\HttpDelete;
use Core\Attributes\Http\HttpGet;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\BodyParameter;
use Core\Attributes\Parameter\QueryParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\NotFoundResponse;
use Core\Contracts\Api\OkResponse;
use Core\Controllers\ApiController;

// TODO: Authorization
/**
 * Represents the controller that is used for the user service.
 */
class UserController extends ApiController
{
    private const END_POINT = "api/user";

    function __construct(private readonly UserServiceInterface $userService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpGet("/list")]
    function getUsers(): OkResponse
    {
        $userEntities = $this->userService->getUsers();
        return new OkResponse($userEntities);
    }

    #[HttpGet]
    function getUserById(#[QueryParameter] int $id): ApiResponse
    {
        $userEntity = $this->userService->getUserById($id);
        return $userEntity != null ? new OkResponse($userEntity) : new NotFoundResponse("User not found");
    }

    #[HttpDelete]
    function deleteUser(#[QueryParameter] int $id): ApiResponse
    {
        $response = $this->userService->deleteUser($id);
        return $response ? new OkResponse($id) : new NotFoundResponse("User not found");
    }

    #[HttpPost("/auth/register")]
    function registerUser(#[BodyParameter] string $payload): ApiResponse
    {
        $request = RegisterUserRequest::deserialize($payload);
        $response = $this->userService->registerUser($request);
        if ($response instanceof ApiResponse)
        {
            return $response;
        }

        return new OkResponse($response);
    }

    #[HttpPost("/auth/login")]
    function loginUser(#[BodyParameter] string $payload): ApiResponse
    {
        $request = LoginUserRequest::deserialize($payload);
        $response = $this->userService->loginUser($request);
        return new OkResponse($response);
    }
}