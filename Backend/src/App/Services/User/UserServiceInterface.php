<?php

namespace App\Services\User;

use App\Contracts\User\UpdateUserSettingsRequest;
use App\Entities\User\UserSettings;
use Core\Contracts\Api\ApiResponse;

/**
 * Represents the service that is used for user operations like updating the entity.
 */
interface UserServiceInterface
{
    function changeSettings(UpdateUserSettingsRequest $request): UserSettings | ApiResponse;
    function logout(): void;
}