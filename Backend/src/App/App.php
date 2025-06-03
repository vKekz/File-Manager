<?php

namespace App;

use App\Controllers\Token\TokenController;
use App\Controllers\User\UserController;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiRequest;
use Core\Controllers\ApiController;
use Core\DependencyInjection\ServiceContainer;
use Core\Enums\HttpMethod;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private readonly HttpContext $httpContext;
    private array $controllers = [];

    function __construct(ServiceContainer $serviceContainer)
    {
        $this->httpContext = $serviceContainer->resolve(HttpContext::class);
        $this->registerController($serviceContainer->resolve(UserController::class));
        $this->registerController($serviceContainer->resolve(TokenController::class));
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