<?php

namespace App\Repositories\User;

use App\Entities\User\UserEntity;
use Core\Database\Database;

/**
 * @inheritdoc
 */
readonly class UserRepository implements UserRepositoryInterface
{
    public const TABLE_NAME = "user_entities";

    function __construct(private Database $database)
    {
    }

    /**
     * @inheritdoc
     */
    function findById(string $id): ?UserEntity
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    function save(UserEntity $entity): void
    {
    }
}