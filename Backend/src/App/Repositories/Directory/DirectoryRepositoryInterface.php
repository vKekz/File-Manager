<?php

namespace App\Repositories\Directory;

use App\Entities\Directory\DirectoryEntity;

interface DirectoryRepositoryInterface
{
    function findById(int $id): ?DirectoryEntity;
}