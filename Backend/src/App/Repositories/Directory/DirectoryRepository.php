<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;
use App\Repositories\User\UserRepository;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class DirectoryRepository implements DirectoryRepositoryInterface
{
    public const TABLE_NAME = "directory_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    /**
     * @inheritdoc
     */
    function findByUserId(int $userId): array
    {
        $condition = "WHERE UserId = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $userId);
    }

    /**
     * @inheritdoc
     */
    function findById(int $id): ?DirectoryEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $id);

        if (count($data) == 0)
        {
            return null;
        }

        return DirectoryEntity::fromArray($data[0]);
    }

    /**
     * @inheritdoc
     */
    function findByNameForUserWithParentId(int $parentId, int $userId, string $name): array
    {
        $condition = "WHERE ParentId = ? AND UserId = ? AND Name = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $parentId, $userId, $name);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(DirectoryEntity $entity): bool
    {
        $attributes = ["Id", "ParentId", "UserId", "Name", "Path", "CreatedAt"];
        $values = [$entity->id, $entity->parentId, $entity->userId, $entity->name, $entity->path, $entity->createdAt];

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
            ParentId bigint NOT NULL,
            UserId bigint NOT NULL,
            Name varchar(255) NOT NULL,
            Path varchar(255) NOT NULL,
            CreatedAt datetime NOT NULL,
            FOREIGN KEY (UserId) REFERENCES " . UserRepository::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}