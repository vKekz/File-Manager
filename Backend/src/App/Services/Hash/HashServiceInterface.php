<?php

namespace App\Services\Hash;

interface HashServiceInterface
{
    function hashPassword(string $input);
    function verifyPassword(string $hash);
}