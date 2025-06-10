<?php

namespace App\Contracts\Directory;

readonly class DeleteDirectoryResponse
{
    function __construct(public string $id)
    {
    }
}