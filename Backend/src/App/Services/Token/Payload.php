<?php

namespace App\Services\Token;

readonly class Payload
{
    function __construct(
        public int $sessionId,
        public int $userId,
        public string $deviceData,
        public string $createdAt
    ) {}

    public static function fromArray(array $data): Payload
    {
        return new self($data["sessionId"], $data["userId"], $data["deviceData"], $data["createdAt"]);
    }

    public function toString(): string
    {
        return json_encode($this);
    }
}