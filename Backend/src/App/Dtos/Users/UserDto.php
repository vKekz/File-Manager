<?php

namespace App\Dtos\Users;

use App\Entities\User\UserEntity;

readonly class UserDto
{
    function __construct(public int $id, public string $username, public string $email)
    {
    }

    public static function fromEntity(UserEntity $entity)
    {
        return new self($entity->id, $entity->username, $entity->email);
    }
}