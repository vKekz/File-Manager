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
    function findById(int $id): ?UserEntity
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
    function tryAdd(UserEntity $entity): bool
    {
        $attributes = ["Id", "Email", "UserName", "PasswordHash", "CreatedAt"];
        $values = [$entity->id, $entity->email, $entity->username, $entity->passwordHash, $entity->createdAt];

        return $this->database->insertData(self::TABLE_NAME, $attributes, $values);
    }

    /**
     * @inheritdoc
     */
    function tryRemove(int $id): bool
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
            Id bigint PRIMARY KEY NOT NULL,
            UserName varchar(32) NOT NULL,
            Email varchar(320) NOT NULL,
            PasswordHash varchar(1024) NOT NULL,
            CreatedAt DATETIME NOT NULL
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}