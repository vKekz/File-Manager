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
    private array $userEntities;

    function __construct(private readonly Database $database)
    {
        $this->userEntities = [];

        // TODO: Lazy load
        $this->fetchUsers();
        $this->createTable();
    }

    function getUsers(): array
    {
        return $this->userEntities;
    }

    /**
     * @inheritdoc
     */
    function findById(int $id): ?UserEntity
    {
        return array_key_exists($id, $this->userEntities) ? $this->userEntities[$id] : null;
    }

    /**
     * @inheritdoc
     */
    function tryAdd(UserEntity $entity): bool
    {
        $attributes = ["Id", "Email", "UserName", "PasswordHash"];
        $values = [$entity->id, $entity->email, $entity->username, $entity->passwordHash];

        $this->userEntities[$entity->id] = $entity;

        return $this->database->insertData(self::TABLE_NAME, $attributes, $values);
    }

    /**
     * @inheritdoc
     */
    function tryRemove(int $id): bool
    {
        $condition = "WHERE Id = ?";
        if (!$this->database->deleteData(self::TABLE_NAME, $condition, $id))
        {
            return false;
        }

        if (!array_key_exists($id, $this->userEntities))
        {
            return false;
        }

        unset($this->userEntities[$id]);

        return true;
    }

    private function fetchUsers(): void
    {
        $data = $this->database->fetchData(self::TABLE_NAME);
        foreach ($data as $entry)
        {
            $this->userEntities[$entry["Id"]] = UserEntity::fromArray($entry);
        }
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