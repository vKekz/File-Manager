<?php

namespace Controllers\Contracts;

use Attributes\Http\HttpMethod;

/**
 * Represents an API request.
 */
readonly class ApiRequest
{
    private string $route;
    private HttpMethod $method;

    function __construct(string $route, string $method)
    {
        $this->route = $route;
        $this->method = HttpMethod::getFromName($method);
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