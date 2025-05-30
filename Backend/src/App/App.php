<?php

namespace App;

use App\Controllers\User\UserController;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Hash\HashService;
use App\Services\Hash\HashServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use Core\Contracts\Api\ApiRequest;
use Core\Controllers\ApiController;
use Core\Database\Database;
use Core\Enums\HttpMethod;
use Core\DependencyInjection\ServiceContainer;
use Core\DependencyInjection\ServiceContainerInterface;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private array $controllers = [];
    private readonly ServiceContainerInterface $serviceContainer;

    function __construct()
    {
        $this->serviceContainer = new ServiceContainer();

        // Register services
        $this->serviceContainer->register(Database::class, Database::class);
        $this->serviceContainer->register(HashServiceInterface::class, HashService::class);
        $this->serviceContainer->register(UserRepositoryInterface::class, UserRepository::class);
        $this->serviceContainer->register(UserServiceInterface::class, UserService::class);

        // Register controllers with its dependencies
        $userService = $this->serviceContainer->resolve(UserServiceInterface::class);
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
            http_response_code(405);
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