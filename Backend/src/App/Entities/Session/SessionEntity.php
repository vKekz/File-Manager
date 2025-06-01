<?php

namespace App\Entities\Session;

readonly class SessionEntity
{
    function __construct(public int $id, public int $userId, public string $sessionId, public string $hash)
    {
    }

    public static function fromArray(array $data): SessionEntity
    {
        return new self($data["Id"], $data["UserId"], $data["SessionId"], $data["Hash"]);
    }
}