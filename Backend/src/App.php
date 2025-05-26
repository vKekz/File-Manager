<?php

use Contracts\Api\ApiRequest;
use Controllers\ApiController;
use Controllers\User\UserController;
use Enums\HttpMethod;
use Services\User\UserService;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private array $controllers = [];

    function __construct()
    {
        $userService = new UserService();
        $this->registerController(new UserController($userService));
    }

    /**
     * Forwards a given request to the corresponding controller that handles the request.
     */
    public function handleRequest(string $route, string $method): void
    {
        if (empty($route))
        {
            return;
        }

        $controller = $this->findControllerByRoute($route);
        if ($controller == null)
        {
            http_response_code(500);
            return;
        }

        $controller->handleRequest(new ApiRequest($route, HttpMethod::getFromName($method)));
    }

    private function registerController(ApiController $controller): void
    {
        $this->controllers[$controller->route] = $controller;
    }

    private function findControllerByRoute(string $route): ?ApiController
    {
        foreach ($this->controllers as $controller)
        {
            if (str_starts_with($route, $controller->route))
            {
                return $controller;
            }
        }

        return null;
    }
}