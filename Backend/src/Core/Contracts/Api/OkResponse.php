<?php

namespace Core\Contracts\Api;

/**
 * Represents an API response with the HTTP status code 200.
 */
class OkResponse extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 200);
    }
}