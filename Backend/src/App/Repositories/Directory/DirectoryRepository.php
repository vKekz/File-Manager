<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;
use App\Repositories\User\UserRepository;
use Core\Database\Database;
use DateTime;

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
    function createRootDirectoryForUser(string $userId): void
    {
        if ($this->doesUserHaveRootDirectory($userId))
        {
            return;
        }

        // For simplicity, root directories will have the user ID as the primary key
        $rootDirectory = new DirectoryEntity(
            $userId,
            0,
            $userId,
            "root",
            "",
            (new DateTime())->format(DATE_ISO8601_EXPANDED),
            true
        );
        $this->tryAdd($rootDirectory);
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?DirectoryEntity
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
    function findByParentIdForUser(string $userId, string $parentId): array
    {
        $condition = "WHERE UserId = ? AND ParentId = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $userId, $parentId);
    }

    /**
     * @inheritdoc
     */
    function findByParentIdAndNameForUser(string $parentId, string $userId, string $name): array
    {
        $condition = "WHERE ParentId = ? AND UserId = ? AND Name = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $parentId, $userId, $name);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(DirectoryEntity $entity): bool
    {
        $attributes = ["Id", "ParentId", "UserId", "Name", "Path", "CreatedAt", "IsRoot"];
        $values = [$entity->id, $entity->parentId, $entity->userId, $entity->name, $entity->path, $entity->createdAt, (int)$entity->isRoot];

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

    private function doesUserHaveRootDirectory(string $userId): bool
    {
        $condition = "WHERE UserId = ? AND IsRoot = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $userId, 1);
        return count($data) == 1;
    }

    private function createTable(): void
    {
        $attributes = "(
            Id varchar(36) PRIMARY KEY NOT NULL,
            ParentId varchar(36) NOT NULL,
            UserId varchar(36) NOT NULL,
            Name varchar(255) NOT NULL,
            Path varchar(255) NOT NULL,
            CreatedAt datetime NOT NULL,
            IsRoot boolean NOT NULL,
            FOREIGN KEY (UserId) REFERENCES " . UserRepository::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}