<?php

namespace App;

use Core\Contracts\Api\ApiRequest;
use Core\Controllers\ApiController;
use Core\Database\Database;
use Core\Enums\HttpMethod;
use App\Controllers\User\UserController;
use App\Repositories\User\UserRepository;
use App\Services\User\UserService;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private array $controllers = [];
    private readonly Database $database;

    function __construct()
    {
        $this->database = new Database();

        $userRepository = new UserRepository($this->database);
        $userService = new UserService($userRepository);
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
        $this->controllers[$controller->endpoint] = $controller;
    }

    private function findControllerByRoute(string $route): ?ApiController
    {
        foreach ($this->controllers as $controller)
        {
            if (str_starts_with($route, $controller->endpoint))
            {
                return $controller;
            }
        }

        return null;
    }
}