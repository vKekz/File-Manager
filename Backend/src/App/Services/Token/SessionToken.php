<?php

namespace App\Services\Token;

readonly class SessionToken
{
    function __construct(public string $payload, public string $signature)
    {
    }

    public static function fromToken(string $accessToken): SessionToken
    {
        [$payload, $signature] = explode(".", $accessToken, 2);
        return new self($payload, $signature);
    }

    public function toString(): string
    {
        return "$this->payload.$this->signature";
    }
}