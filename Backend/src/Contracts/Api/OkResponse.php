<?php

namespace Contracts\Api;

/**
 * Represents an API response with the HTTP status code 200.
 */
class OkResponse extends ApiResponse
{
    function __construct(mixed $data)
    {
        parent::__construct($data, 200);
    }
}