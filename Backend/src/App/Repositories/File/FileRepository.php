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
    function findByParentIdAndNameHashForUser(string $directoryId, string $userId, string $nameHash): array
    {
        $condition = "WHERE DirectoryId = ? AND UserId = ? AND NameHash = ?";
        return $this->database->fetchData(self::TABLE_NAME, [], $condition, $directoryId, $userId, $nameHash);
    }

    /**
     * @inheritdoc
     */
    function tryUpdate(FileEntity $file): bool
    {
        $condition = "WHERE Id = ?";
        $attributes = ["RealHash", "Hash", "Size", "UploadedAt"];
        $values = [$file->realHash, $file->hash, $file->size, $file->uploadedAt, $file->id];

        return $this->tryUpdateEntity($attributes, $values, $condition);
    }

    /**
     * @inheritdoc
     */
    function tryAdd(FileEntity $entity): bool
    {
        $attributes = ["Id", "DirectoryId", "UserId", "Name", "NameHash", "RealHash", "Hash", "Size", "UploadedAt",];
        $values = [
            $entity->id,
            $entity->directoryId,
            $entity->userId,
            $entity->name,
            $entity->nameHash,
            $entity->realHash,
            $entity->hash,
            $entity->size,
            $entity->uploadedAt
        ];

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

    private function tryUpdateEntity(array $attributes, array $values, string $condition = ""): bool
    {
        return $this->database->updateData(self::TABLE_NAME, $attributes, $condition, $values);
    }

    private function createTable(): void
    {
        $attributes = "(
            Id varchar(36) PRIMARY KEY NOT NULL,
            DirectoryId varchar(36) NOT NULL,
            UserId varchar(36) NOT NULL,
            Name varchar(1024) NOT NULL,
            NameHash varchar(128) NOT NULL,
            RealHash varchar(64) NOT NULL,
            Hash varchar(64) NOT NULL,
            Size int NOT NULL,
            UploadedAt datetime NOT NULL,
            FOREIGN KEY (DirectoryId) REFERENCES " . DirectoryRepository::TABLE_NAME . "(Id),
            FOREIGN KEY (UserId) REFERENCES " . UserRepository::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}