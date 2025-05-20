<?php

namespace Controllers\Contracts;

use Controllers\ApiController;

/**
 * Represents a method for a given controller that can be called.
 */
readonly class ControllerMethod
{
    private string $name;
    private ApiController $controller;

    function __construct(string $name, ApiController $controller)
    {
        $this->name = $name;
        $this->controller = $controller;
    }

    /**
     * Calls the method in the controller if it exists.
     */
    public function call(): void
    {
        if (!method_exists($this->controller, $this->name)) {
            return;
        }

        call_user_func([$this->controller, $this->name]);
    }
}