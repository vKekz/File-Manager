<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;
use Core\Database\Database;

class DirectoryRepository implements DirectoryRepositoryInterface
{
    public const TABLE_NAME = "directory_entities";

    function __construct(private readonly Database $database)
    {
        $this->createTable();
    }

    function findById(int $id): ?DirectoryEntity
    {
        $condition = "WHERE Id = ?";
        $data = $this->database->fetchData(self::TABLE_NAME, condition: $condition, values: $id);

        if (count($data) == 0)
        {
            return null;
        }

        return DirectoryEntity::fromArray($data[0]);
    }

    private function createTable(): void
    {
        $attributes = "(
            Id bigint PRIMARY KEY NOT NULL,
            ParentId bigint,
            Name varchar(255) NOT NULL,
            Path varchar(255) NOT NULL,
            CreatedAt datetime NOT NULL,
            FOREIGN KEY (ParentId) REFERENCES " . self::TABLE_NAME . "(Id)
        );";
        $this->database->createTable(self::TABLE_NAME, $attributes);
    }
}