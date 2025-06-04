<?php

namespace App\Services\Session\Token;

use App\Services\Session\Enums\ClaimKey;

readonly class Payload
{
    function __construct(public array $claims)
    {
    }

    public function getClaim(ClaimKey $key): false | string
    {
        return $this->claims[$key->value] ?? false;
    }
}