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

        // TODO: Lazy load
        $this->fetchUsers();
    }

    public function getUsers(): array
    {
        return $this->userEntities;
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?UserEntity
    {
        return array_key_exists($id, $this->userEntities) ? $this->userEntities[$id] : null;
    }

    /**
     * @inheritdoc
     */
    function trySave(UserEntity $entity): bool
    {
        $attributes = ["Id", "Email", "UserName", "PasswordHash"];
        $values = [$entity->id, $entity->email, $entity->userName, $entity->passwordHash];

        return $this->database->insertData(self::TABLE_NAME, $attributes, $values);
    }

    private function fetchUsers(): void
    {
        $data = $this->database->fetchData(self::TABLE_NAME);
        foreach ($data as $entry)
        {
            $this->userEntities[$entry["Id"]] = UserEntity::fromArray($entry);
        }
    }
}