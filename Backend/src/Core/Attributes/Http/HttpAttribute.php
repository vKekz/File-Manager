<?php

namespace Core\Attributes\Http;

use Core\Enums\HttpMethod;

/**
 * Represents the base class for an HTTP attribute.
 */
abstract class HttpAttribute
{
    function __construct(public string $route, public HttpMethod $method)
    {
    }
}