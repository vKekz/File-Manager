<?php

namespace Core\Attributes\Http;

use Attribute;
use Core\Enums\HttpMethod;

/**
 * Marks a method to support the HTTP POST method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpAttribute
{
    function __construct(string $route = "")
    {
        parent::__construct($route, HttpMethod::Post);
    }
}