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

    public function fetchData(string $table, array $attributes = [], string $condition = "", string ...$values): array
    {
        $attributes = count($attributes) == 0 ? "*" : join(", ", $attributes);
        $query = "SELECT $attributes FROM $table $condition";

        $result = count($values) == 0 ?
            $this->selectQuery($query) :
            $this->selectQuery($query, join(", ", $values));

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
        // Join attributes by comma "Attr, Attr, ..., Attr"
        $attributes = join(", ", $attributes);

        // Create array of marks ["?", ..., "?"]
        $marks = str_split(str_repeat("?", count($values)));

        // Join marks by comma "?, ?, ..., ?"
        $valuesQuery = join(", ", $marks);

        // Finally generate query
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
        if (count($args) == 0)
        {
            return $statement;
        }

        // Generate string of types for SQL query e.g. "sis"
        $types = "";
        foreach ($args as $arg)
        {
            $type = str_split(gettype($arg))[0] ?? "?";
            $types = "$types$type";
        }

        $statement->bind_param($types, ...$args);
        return $statement;
    }

    private function selectQuery(string $query, mixed ...$args): mysqli_result | false
    {
        $statement = $this->connection->prepare($query);
        $statement = $this->bindParameters($statement, ...$args);

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
        $statement = $this->bindParameters($statement, ...$args);

        $result = $statement->execute();
        $statement->close();
        return $result;
    }
}