<?php

namespace Core\Controllers;


use Core\Contracts\Api\ApiResponse;

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
        // Making use of https://wiki.php.net/rfc/argument_unpacking
        return call_user_func([$this->controller, $this->name], ...$arguments);
    }
}