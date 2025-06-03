<?php

namespace App\Services\Session;

use App\Entities\User\UserEntity;
use App\Services\Token\SessionToken;
use Core\Contracts\Api\ServerErrorResponse;

interface SessionServiceInterface
{
    function createSession(UserEntity $userEntity): SessionToken | ServerErrorResponse;
    function validateAccessToken(string $accessToken): bool;
}