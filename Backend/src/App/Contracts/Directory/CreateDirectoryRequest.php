<?php

namespace App\Contracts\Directory;

readonly class CreateDirectoryRequest
{
    function __construct(
        public string $name,
        public int $parentId
    )
    {
    }
}