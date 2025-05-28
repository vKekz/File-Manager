<?php

namespace App\Controllers\User;

use Core\Attributes\Http\HttpGet;
use Core\Attributes\Http\HttpPost;
use Core\Attributes\Parameter\BodyParameter;
use Core\Attributes\Parameter\QueryParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\NotFoundResponse;
use Core\Contracts\Api\OkResponse;
use Core\Controllers\ApiController;
use App\Contracts\User\RegisterUserRequest;
use App\Services\User\UserServiceInterface;

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

    #[HttpGet]
    public function getUserById(#[QueryParameter] string $id): ApiResponse
    {
        $userEntity = $this->userService->getUserById($id);
        return $userEntity != null ? new OkResponse($userEntity) : new NotFoundResponse(null);
    }

    #[HttpGet("/list")]
    public function getUsers(): OkResponse
    {
        $userEntities = $this->userService->getUsers();
        return new OkResponse($userEntities);
    }

    #[HttpPost("/register")]
    public function registerUser(#[BodyParameter] string $payload): OkResponse
    {
        $request = RegisterUserRequest::deserialize($payload);
        $response = $this->userService->registerUser($request);
        return new OkResponse($response);
    }
}