<?php

namespace Controllers\User;

use Attributes\Http\Types\HttpGet;
use Attributes\Http\Types\HttpPost;
use Attributes\Parameter\BodyParameter;
use Contracts\Api\OkResponse;
use Contracts\User\RegisterUserRequest;
use Controllers\ApiController;
use Services\User\UserServiceInterface;

/**
 * Represents the controller that is used for the user service.
 */
class UserController extends ApiController
{
    private const ROUTE = "api/user";

    function __construct(private readonly UserServiceInterface $userService)
    {
        parent::__construct(self::ROUTE);
    }

    #[HttpGet("/all")]
    public function getUsers(): OkResponse
    {
        return new OkResponse($this->userService->getUsers());
    }

    #[HttpPost("/register")]
    public function registerUser(#[BodyParameter] RegisterUserRequest $request): OkResponse
    {
        $response = $this->userService->registerUser($request);
        return new OkResponse($response);
    }
}