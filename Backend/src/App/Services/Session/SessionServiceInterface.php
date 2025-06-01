<?php

namespace App\Services\Session;

use App\Entities\Session\SessionEntity;
use App\Entities\User\UserEntity;
use Core\Contracts\Api\ServerErrorResponse;

interface SessionServiceInterface
{
    function createSession(UserEntity $userEntity): SessionEntity | ServerErrorResponse;
}