<?php

namespace Contracts\Api;

use Enums\HttpMethod;

/**
 * Represents an API request.
 */
readonly class ApiRequest
{
    function __construct(public string $route, public HttpMethod $method)
    {
    }
}