<?php

namespace Attributes\Http;

/**
 * Represents the base class for an HTTP attribute.
 */
abstract class HttpMethodAttribute
{
    /**
     * The route to this method.
     */
    private string $route;

    /**
     * The HTTP method.
     */
    private HttpMethod $method;

    function __construct(string $route, HttpMethod $method)
    {
        $this->route = $route;
        $this->method = $method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getMethod(): HttpMethod
    {
        return $this->method;
    }
}