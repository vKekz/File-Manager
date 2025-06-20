<?php

namespace App\Repositories\Session;

use App\Entities\Session\SessionEntity;
use App\Repositories\User\UserRepository;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class SessionRepository implements SessionRepositoryInterface
{
    private const TABLE_NAME = "session_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?SessionEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $id);

        if (count($data) == 0)
        {
            return null;
        }

        return SessionEntity::fromArray($data[0]);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(SessionEntity $entity): bool
    {
        $attributes = ["Id", "UserId", "DeviceData", "IssuedAt", "ExpiresAt"];
        $values = [$entity->id, $entity->userId, $entity->deviceData, $entity->issuedAt, $entity->expiresAt];

        return $this->database->insertData(self::TABLE_NAME, $attributes, $values);
    }

    /**
     * @inheritdoc
     */
    function tryRemove(string $id): bool
    {
        return false;
    }

    private function createTable(): void
    {
        $attributes = "(
            Id varchar(36) PRIMARY KEY NOT NULL,
            UserId varchar(36) NOT NULL,
            DeviceData varchar(256) NOT NULL,
            IssuedAt datetime NOT NULL,
            ExpiresAt datetime NOT NULL,
            FOREIGN KEY (UserId) REFERENCES " . UserRepository::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}