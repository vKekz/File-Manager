<?php

namespace Core\Database;

use mysqli;
use mysqli_result;
use mysqli_stmt;

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
    }

    public function close(): bool
    {
        return $this->connection->close();
    }

    public function createTable(string $tableName, string $attributes): void
    {
        $query = "CREATE TABLE IF NOT EXISTS $tableName $attributes";
        $statement = $this->connection->prepare($query);

        if (!$statement->execute())
        {
            $statement->close();
            die("Failed creating table: $tableName");
        }

        $statement->close();
    }

    public function fetchData(string $table, string ...$attributes): array
    {
        $attributes = count($attributes) == 0 ? "*" : join(", ", $attributes);
        $query = "SELECT $attributes FROM $table";
        $result = $this->selectQuery($query);

        // Return empty array if result was false
        if (!$result)
        {
            return [];
        }

        $data = [];
        while ($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }

        $result->free();
        return $data;
    }

    public function insertData(string $table, array $attributes, array $values): bool
    {
        $attributes = join(", ", $attributes);
        $marks = str_split(str_repeat("?", count($values)));
        $valuesQuery = join(", ", $marks);
        $query = "INSERT INTO $table ($attributes) VALUES ($valuesQuery)";

        return $this->executePreparedQuery($query, ...$values);
    }

    public function deleteData(string $table, string $condition, string ...$values): bool
    {
        $query = "DELETE FROM $table $condition";
        return $this->executePreparedQuery($query, ...$values);
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

    private function bindParameters(mysqli_stmt $statement, mixed ...$args): mysqli_stmt
    {
        $arguments = $args[0];
        if (count($arguments) == 0)
        {
            return $statement;
        }

        $types = "";
        foreach ($arguments as $arg)
        {
            $type = str_split(gettype($arg))[0] ?? "?";
            $types = "$types$type";
        }

        $statement->bind_param($types, ...$arguments);
        return $statement;
    }

    private function selectQuery(string $query, mixed ...$args): mysqli_result | false
    {
        $statement = $this->connection->prepare($query);
        $statement = $this->bindParameters($statement, $args);

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

    private function executePreparedQuery(string $query, mixed ...$args): bool
    {
        $statement = $this->connection->prepare($query);
        $statement = $this->bindParameters($statement, $args);

        $result = $statement->execute();
        $statement->close();
        return $result;
    }
}