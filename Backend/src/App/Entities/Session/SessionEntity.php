<?php

namespace App\Entities\Session;

readonly class SessionEntity
{
    function __construct(
        public int $id,
        public int $userId,
        public string $deviceData,
        public string $createdAt,
        public string $expiresAt
    )
    {
    }

    public static function fromArray(array $data): SessionEntity
    {
        return new self($data["Id"], $data["UserId"], $data["DeviceData"], $data["CreatedAt"], $data["ExpiresAt"]);
    }
}