<?php

namespace Core\Contracts\Api;

/**
 * Represents an API response with the HTTP status code 404.
 */
class NotFoundResponse extends ApiResponse
{
    function __construct(mixed $data = "")
    {
        parent::__construct($data, 404);
    }
}