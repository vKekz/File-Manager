<?php

namespace Core\Authorization;

readonly class AuthorizationToken
{
    function __construct(public string $type, public string $token)
    {
    }

    public static function fromHeader(?string $header): ?AuthorizationToken
    {
        if ($header == null)
        {
            return null;
        }

        [$type, $token] = explode(" ", $header, 2);
        return new self($type, $token);
    }
}