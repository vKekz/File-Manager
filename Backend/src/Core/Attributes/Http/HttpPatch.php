<?php

namespace Core\Attributes\Http;

use Attribute;
use Core\Enums\HttpMethod;

/**
 * Marks a method to support the HTTP PATCH method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPatch extends HttpAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Patch);
    }
}