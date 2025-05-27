<?php

namespace Core\Attributes\Http;

use Attribute;
use Core\Enums\HttpMethod;

/**
 * Marks a method to support the HTTP DELETE method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpDelete extends HttpAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Delete);
    }
}