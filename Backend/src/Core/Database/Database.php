<?php

namespace Core\Database;

use mysqli;
use mysqli_result;
use App\Repositories\User\UserRepository;

readonly class Database
{
    private const DATABASE_NAME = "file_manager";
    private mysqli $connection;

    function __construct()
    {
        // TODO: .env
        $this->connection = new mysqli("localhost", "root", "");

        $error = $this->connection->connect_error;
        if ($error)
        {
            die("Failed establishing connection to database: " . $error);
        }

        $this->createDatabase();
        $this->createUserEntityTable();
    }

    public function close(): bool
    {
        return $this->connection->close();
    }

    public function fetchData(string $table, string ...$attributes): array
    {
        $attributes = count($attributes) == 0 ? "*" : join(", ", $attributes);
        $query = $this->selectQuery("SELECT " . $attributes . " FROM " . $table);
        if (!$query)
        {
            return [];
        }

        $data = [];
        while ($row = $query->fetch_assoc())
        {
            $data[] = $row;
        }

        $query->free();
        return $data;
    }

    private function selectQuery(string $query, string $types = "", mixed ...$args): mysqli_result | false
    {
        $statement = $this->connection->prepare($query);
        if (!empty($types))
        {
            $statement->bind_param($types, ...$args);
        }

        if ($statement->execute())
        {
            $result = $statement->get_result();
            $statement->close();

            if ($result->num_rows == 0)
            {
                return false;
            }

            return $result;
        }

        $statement->close();
        return false;
    }

    private function createDatabase(): void
    {
        $query = "CREATE DATABASE IF NOT EXISTS " . self::DATABASE_NAME;
        $statement = $this->connection->prepare($query);

        if (!$statement->execute())
        {
            $statement->close();
            die("Failed creating database: " . self::DATABASE_NAME);
        }

        $statement->close();
        $this->connection->select_db(self::DATABASE_NAME);
    }

    private function createUserEntityTable(): void
    {
        $attributes = "(
            Id varchar(50) PRIMARY KEY,
            UserName varchar(16),
            Email varchar(50),
            PasswordHash varchar(255)
        )";
        $this->createTable(UserRepository::TABLE_NAME, $attributes);
    }

    private function createTable(string $tableName, string $attributes): void
    {
        $query = "CREATE TABLE IF NOT EXISTS " . $tableName . $attributes;
        $statement = $this->connection->prepare($query);

        if (!$statement->execute())
        {
            $statement->close();
            die("Failed creating table: " . $tableName);
        }

        $statement->close();
    }
}