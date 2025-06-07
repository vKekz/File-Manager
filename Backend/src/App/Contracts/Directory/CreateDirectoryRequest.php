<?php

namespace App\Contracts\Directory;

readonly class CreateDirectoryRequest
{
    function __construct(public string $name, public string $parentId)
    {
    }

    public static function deserialize(string $json): CreateDirectoryRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["name"], $decoded["parentId"]);
    }
}