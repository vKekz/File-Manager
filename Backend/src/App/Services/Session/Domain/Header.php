<?php

namespace App\Services\Session\Domain;

readonly class Header
{
    function __construct(public string $alg, public string $typ = "JWT")
    {
    }

    public static function fromArray(array $array): Header
    {
        return new self($array["alg"], $array["typ"]);
    }
}