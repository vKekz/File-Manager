<?php

namespace App\Controllers\User;

use App\Contracts\User\UpdateUserSettingsRequest;
use App\Services\User\UserServiceInterface;
use Core\Attributes\Authorization\Authorize;
use Core\Attributes\Http\HttpPatch;
use Core\Attributes\Parameter\BodyParameter;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\Ok;
use Core\Controllers\ApiController;

#[Authorize]
class UserController extends ApiController
{
    private const END_POINT = "api/user";

    function __construct(private readonly UserServiceInterface $userService)
    {
        parent::__construct(self::END_POINT);
    }

    #[HttpPatch("/settings")]
    function updateSettings(#[BodyParameter] string $body): ApiResponse
    {
        $request = UpdateUserSettingsRequest::deserialize($body);
        $response = $this->userService->changeSettings($request);
        return $response instanceof ApiResponse ? $response : new Ok($response);
    }
}