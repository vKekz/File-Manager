<?php

use Controllers\ApiController;
use Controllers\User\UserController;

/**
 * Represents the main Application that will handle all requests.
 */
class App
{
    private array $controllers;

    function __construct()
    {
        $this->controllers = [];
        $this->registerController(new UserController);
    }

    public function handleRequest(string $requestRoute): void
    {
        if (empty($requestRoute) || !array_key_exists($requestRoute, $this->controllers)) {
            return;
        }

        // TODO: Get methods of controller with HTTP attributes to get route
        $controller = $this->controllers[$requestRoute];
        var_dump(gettype($controller));
    }

    private function registerController(ApiController $controller): void
    {
        $route = $controller->getRoute();
        $this->controllers[$route] = $controller;
    }
}