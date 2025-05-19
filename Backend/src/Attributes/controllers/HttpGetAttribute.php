<?php

namespace Attributes\Controllers;

use Attribute;

/**
 * Marks a method to support the HTTP GET method.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class HttpGetAttribute
{
    /**
     * The route to this GET method.
     */
    private string $route;

    function __construct(string $route)
    {
        $this->route = $route;
    }

    public function getRoute(): string
    {
        return $this->route;
    }
}