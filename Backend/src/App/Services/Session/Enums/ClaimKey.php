<?php

namespace App\Services\Session\Enums;

enum ClaimKey: string
{
    case Subject = "sub";
    case IssuedAt = "iat";
    case ExpiresAt = "exp";
    case SessionId = "sessionId";
}