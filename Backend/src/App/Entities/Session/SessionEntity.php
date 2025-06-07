<?php

namespace App\Entities\Session;

/**
 * Represents the database entity for sessions.
 */
readonly class SessionEntity
{
    function __construct(
        public string $id,
        public string $userId,
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