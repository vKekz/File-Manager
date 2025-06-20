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
    function findByEmail(string $email): ?UserEntity
    {
        $condition = "WHERE Email = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $email);

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
        $attributes = ["Id", "Email", "UserName", "Hash", "PrivateKey", "CreatedAt", "Settings"];
        $values = [$entity->id, $entity->email, $entity->username, $entity->hash, $entity->privateKey, $entity->createdAt, $entity->settings->serialize()];

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
            UserName varchar(16) NOT NULL,
            Email varchar(320) NOT NULL,
            Hash varchar(255) NOT NULL,
            PrivateKey varchar(255) NOT NULL,
            CreatedAt datetime NOT NULL,
            Settings json NOT NULL
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}