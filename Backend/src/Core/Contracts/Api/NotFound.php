<?php

namespace Core\Contracts\Api;

/**
 * Represents an API response with the HTTP status code 404.
 */
class NotFound extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 404);
    }
}