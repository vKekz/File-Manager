<?php

namespace Core\Contracts\Api;

/**
 * Represents a method not allowed response with the HTTP status code 405.
 */
class MethodNotAllowed extends ApiResponse
{
    function __construct(mixed $message = "Method not allowed")
    {
        parent::__construct($message, 405);
    }
}