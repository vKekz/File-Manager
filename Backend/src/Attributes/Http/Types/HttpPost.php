<?php

namespace Attributes\Http\Types;

use Attribute;
use Attributes\Http\HttpMethodAttribute;
use Enums\HttpMethod;

/**
 * Marks a method to support the HTTP POST method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpMethodAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Post);
    }
}