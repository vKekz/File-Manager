<?php

namespace App\Services\Session;

use App\Entities\User\UserEntity;
use App\Services\Session\Domain\SessionToken;
use Core\Contracts\Api\InternalServerError;

interface SessionServiceInterface
{
    function createSession(UserEntity $userEntity): SessionToken | InternalServerError;
}