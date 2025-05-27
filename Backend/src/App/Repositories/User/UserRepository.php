<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;
use Core\Database\Database;

/**
 * @inheritdoc
 */
class UserRepository implements UserRepositoryInterface
{
    public const TABLE_NAME = "user_entities";
    private array $userEntities;

    function __construct(private readonly Database $database)
    {
        $this->userEntities = [];
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?UserEntity
    {
        $this->fetchUsers();
        return array_key_exists($id, $this->userEntities) ? $this->userEntities[$id] : null;
    }

    /**
     * @inheritdoc
     */
    function save(UserEntity $entity): void
    {
    }

    private function fetchUsers(): void
    {
        $data = $this->database->fetchData(self::TABLE_NAME);
        foreach ($data as $entry)
        {
            $this->userEntities[$entry["Id"]] = UserEntity::fromQuery($entry);
        }
    }
}