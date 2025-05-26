<?php

namespace Controllers\User;

use Attributes\Http\Types\HttpGet;
use Attributes\Http\Types\HttpPost;
use Attributes\Parameter\Types\BodyParameter;
use Attributes\Parameter\Types\QueryParameter;
use Contracts\Api\OkResponse;
use Contracts\User\RegisterUserRequest;
use Controllers\ApiController;
use Services\User\UserServiceInterface;

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
    public function getUserById(#[QueryParameter] string $id): OkResponse
    {
        return new OkResponse($this->userService->getUserById($id));
    }

    #[HttpPost("/register")]
    public function registerUser(#[BodyParameter] string $payload): OkResponse
    {
        $request = RegisterUserRequest::deserialize($payload);
        $response = $this->userService->registerUser($request);
        return new OkResponse($response);
    }
}