<?php

namespace Controllers;


use Contracts\Api\ApiResponse;

/**
 * Represents a method for a given API controller that can be called.
 */
readonly class ApiControllerMethod
{
    function __construct(private ApiController $controller, private string $name, public array $parameters = [])
    {
    }

    /**
     * Calls the method in the controller and returns the response.
     */
    public function call(mixed ...$arguments): ApiResponse
    {
        return call_user_func([$this->controller, $this->name], $arguments);
    }
}