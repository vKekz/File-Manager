<?php

namespace Attributes\Http\Methods;

use Attribute;
use Attributes\Http\HttpMethod;
use Attributes\Http\HttpMethodAttribute;

/**
 * Marks a method to support the HTTP DELETE method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpDeleteAttribute extends HttpMethodAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Delete);
    }
}