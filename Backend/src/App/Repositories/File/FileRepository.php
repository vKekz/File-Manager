<?php

namespace App\Repositories\File;

use App\Entities\File\FileEntity;
use App\Repositories\Directory\DirectoryRepository;
use App\Repositories\User\UserRepository;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class FileRepository implements FileRepositoryInterface
{
    public const TABLE_NAME = "file_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?FileEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, [], $condition, $id);

        if (count($data) == 0)
        {
            return null;
        }

        return FileEntity::fromArray($data[0]);
    }

    /**
     * @inheritdoc
     */
    function findByDirectoryIdForUser(string $userId, string $directoryId): array
    {
        $condition = "WHERE UserId = ? AND DirectoryId = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $userId, $directoryId);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(FileEntity $entity): bool
    {
        $attributes = ["Id", "DirectoryId", "UserId", "Name", "Path", "Hash", "Size", "UploadedAt",];
        $values = [$entity->id, $entity->directoryId, $entity->userId, $entity->name, $entity->path, $entity->hash, $entity->size, $entity->uploadedAt];

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
            DirectoryId varchar(36) NOT NULL,
            UserId varchar(36) NOT NULL,
            Name varchar(255) NOT NULL,
            Path varchar(255) NOT NULL,
            Hash varchar(255) NOT NULL,
            Size int NOT NULL,
            UploadedAt datetime NOT NULL,
            FOREIGN KEY (DirectoryId) REFERENCES " . DirectoryRepository::TABLE_NAME . "(Id),
            FOREIGN KEY (UserId) REFERENCES " . UserRepository::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}