<?php

namespace App\Services\Hash;

interface HashServiceInterface
{
    function generatePasswordHash(string $input): string;
    function verifyPassword( string $hash, string $userPassword): bool;
}