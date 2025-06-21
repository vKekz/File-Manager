<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class UserRepository implements UserRepositoryInterface
{
    public const TABLE_NAME = "user_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?UserEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $id);

        if (count($data) == 0)
        {
            return null;
        }

        return UserEntity::fromArray($data[0]);
    }

    /**
     * @inheritdoc
     */
    function findByEmailHash(string $emailHash): ?UserEntity
    {
        $condition = "WHERE EmailHash = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $emailHash);

        if (count($data) == 0)
        {
            return null;
        }

        return UserEntity::fromArray($data[0]);
    }

    /**
     * @inheritdoc
     */
    function tryUpdate(array $attributes, array $values, string $condition = ""): bool
    {
        return $this->database->updateData(self::TABLE_NAME, $attributes, $condition, $values);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(UserEntity $entity): bool
    {
        $attributes = ["Id", "UserName", "Email", "EmailHash", "PasswordHash", "PrivateKey", "CreatedAt", "Settings"];
        $values = [$entity->id, $entity->username, $entity->email, $entity->emailHash, $entity->passwordHash, $entity->privateKey, $entity->createdAt, $entity->settings->serialize()];

        return $this->database->insertData(self::TABLE_NAME, $attributes, $values);
    }

    /**
     * @inheritdoc
     */
    function tryRemove(string $id): bool
    {
        if (!$this->findById($id))
        {
            return false;
        }

        $condition = "WHERE Id = ?";
        if (!$this->database->deleteData(self::TABLE_NAME, $condition, $id))
        {
            return false;
        }

        return true;
    }

    private function createTable(): void
    {
        $attributes = "(
            Id varchar(36) PRIMARY KEY NOT NULL,
            UserName varchar(1024) NOT NULL,
            Email varchar(1024) NOT NULL,
            EmailHash varchar(128) NOT NULL,
            PasswordHash varchar(1024) NOT NULL,
            PrivateKey varchar(255) NOT NULL,
            CreatedAt datetime NOT NULL,
            Settings json NOT NULL
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}