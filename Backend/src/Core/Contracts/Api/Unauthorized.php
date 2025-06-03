<?php

namespace Core\Contracts\Api;

/**
 * Represents an Unauthorized response with the HTTP status code 401.
 */
class Unauthorized extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 401);
    }
}