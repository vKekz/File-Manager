<?php

namespace App\Services\Session;

use App\Entities\User\UserEntity;
use App\Services\Session\Token\SessionToken;
use Core\Contracts\Api\InternalServerError;

interface SessionServiceInterface
{
    function createSession(UserEntity $userEntity): SessionToken | InternalServerError;
}