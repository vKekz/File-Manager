<?php

namespace App\Entities\Session;

/**
 * Represents a session entity.
 */
readonly class SessionEntity
{
    function __construct(
        public int $id,
        public int $userId,
        public string $deviceData,
        public string $issuedAt,
        public string $expiresAt
    )
    {
    }

    public static function fromArray(array $data): SessionEntity
    {
        return new self($data["Id"], $data["UserId"], $data["DeviceData"], $data["IssuedAt"], $data["ExpiresAt"]);
    }
}