<?php

namespace Attributes\Http\Types;

use Attribute;
use Attributes\Http\HttpMethodAttribute;
use Enums\HttpMethod;

/**
 * Marks a method to support the HTTP GET method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpGet extends HttpMethodAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Get);
    }
}