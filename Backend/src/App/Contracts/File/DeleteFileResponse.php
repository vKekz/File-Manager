<?php

namespace App\Contracts\File;

readonly class DeleteFileResponse
{
    function __construct(public string $id)
    {
    }
}