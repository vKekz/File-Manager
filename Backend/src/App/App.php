<?php

namespace App;

use App\Controllers\Auth\AuthController;
use App\Controllers\Directory\DirectoryController;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiRequest;
use Core\Controllers\ApiController;
use Core\Database\Database;
use Core\DependencyInjection\ServiceContainer;
use Core\Enums\HttpMethod;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private readonly HttpContext $httpContext;
    private array $controllers = [];

    function __construct(private readonly ServiceContainer $serviceContainer)
    {
        $this->httpContext = $serviceContainer->resolve(HttpContext::class);
        $this->registerController($serviceContainer->resolve(AuthController::class));
        $this->registerController($serviceContainer->resolve(DirectoryController::class));
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

        $controller->handleRequest(new ApiRequest($route, HttpMethod::getFromName($method)), $this->httpContext);
        $this->cleanup();
    }

    private function cleanup(): void
    {
        $database = $this->serviceContainer->resolve(Database::class);
        $database->close();
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