<?php

namespace Core\Attributes\Http;

use Attribute;
use Core\Enums\HttpMethod;

/**
 * Marks a method to support the HTTP GET method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpGet extends HttpAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Get);
    }
}