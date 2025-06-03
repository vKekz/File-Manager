<?php

namespace App\Services\Token;

use App\Entities\Session\SessionEntity;

interface TokenServiceInterface
{
    function generateCsrfToken(): string;
    function generateSessionToken(SessionEntity $sessionEntity): SessionToken;
}