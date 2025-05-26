<?php

namespace Contracts\Api;

/**
 * Represents an API response.
 */
abstract class ApiResponse
{
    function __construct(public mixed $data, public int $statusCode)
    {
    }
}