<?php

namespace App\Contracts\Session;

use App\Dtos\Users\UserDto;

readonly class SessionResponse
{
    function __construct(public UserDto $user, public string $accessToken)
    {
    }
}