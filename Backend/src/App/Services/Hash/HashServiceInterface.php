<?php

interface HashServiceInterface
{
    function hashPassword(string $input);
    function verifyPassword(string $hash);
}