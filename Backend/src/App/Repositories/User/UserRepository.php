<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class UserRepository implements UserRepositoryInterface
{
    private const TABLE_NAME = "user_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    /**
     * @inheritdoc
     */
    function getUsers(): array
    {
        return $this->database->fetchData(self::TABLE_NAME);
    }

    /**
     * @inheritdoc
     */
    function findById(int $id): ?UserEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, condition: $condition, values: $id);

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
        $data = $this->database->fetchData(self::TABLE_NAME, condition: $condition, values: $email);

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
        $attributes = ["Id", "Email", "UserName", "PasswordHash"];
        $values = [$entity->id, $entity->email, $entity->username, $entity->passwordHash];

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
            Id bigint PRIMARY KEY,
            UserName varchar(16),
            Email varchar(50),
            PasswordHash varchar(255)
        )";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}