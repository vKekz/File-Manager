<?php

namespace Attributes\Http\Types;

use Attribute;
use Attributes\Http\HttpMethodAttribute;
use Enums\HttpMethod;

/**
 * Marks a method to support the HTTP DELETE method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpDelete extends HttpMethodAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Delete);
    }
}