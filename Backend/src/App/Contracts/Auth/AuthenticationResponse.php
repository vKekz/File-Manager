<?php

namespace App\Contracts\Auth;

use App\Dtos\Users\UserDto;

readonly class AuthenticationResponse
{
    function __construct(public UserDto $user, public string $accessToken)
    {
    }
}