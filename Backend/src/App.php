<?php

use Controllers\ApiController;
use Controllers\Contracts\Api\ApiRequest;
use Controllers\User\UserController;

/**
 * Represents the main Application that will forward all requests to the corresponding controller.
 */
class App
{
    private array $controllers;

    function __construct()
    {
        $this->controllers = [];
        $this->registerController(new UserController);
    }

    /**
     * Forwards a given request to the corresponding controller that handles the request.
     */
    public function forwardRequestToController(string $route, string $method): void
    {
        if (empty($route))
        {
            exit;
        }

        $controller = $this->findControllerByRoute($route);
        if ($controller == null)
        {
            http_response_code(500);
            exit;
        }

        $controller->handleRequest(new ApiRequest($route, $method));
    }

    private function registerController(ApiController $controller): void
    {
        $route = $controller->getRoute();
        $this->controllers[$route] = $controller;
    }

    private function findControllerByRoute(string $route): ?ApiController
    {
        foreach ($this->controllers as $controller)
        {
            if (str_starts_with($route, $controller->getRoute()))
            {
                return $controller;
            }
        }

        return null;
    }
}