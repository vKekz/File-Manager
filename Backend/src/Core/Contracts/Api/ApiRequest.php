<?php

namespace Core\Contracts\Api;

use Core\Enums\HttpMethod;

/**
 * Represents an API request.
 */
readonly class ApiRequest
{
    function __construct(public string $route, public HttpMethod $method)
    {
    }
}