<?php

namespace Controllers;

use Controllers\Contracts\Api\ApiResponse;

/**
 * Represents a method for a given API controller that can be called.
 */
readonly class ApiControllerMethod
{
    private string $name;
    private ApiController $controller;

    function __construct(string $name, ApiController $controller)
    {
        $this->name = $name;
        $this->controller = $controller;
    }

    /**
     * Calls the method in the controller and returns the response.
     */
    public function call(): ApiResponse
    {
        return call_user_func([$this->controller, $this->name]);
    }
}