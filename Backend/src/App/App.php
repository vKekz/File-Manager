<?php

namespace App;

use App\Controllers\Auth\AuthController;
use App\Controllers\Directory\DirectoryController;
use App\Controllers\File\FileController;
use App\Controllers\Session\SessionController;
use App\Controllers\Storage\StorageController;
use App\Controllers\User\UserController;
use App\Services\Session\SessionServiceInterface;
use Core\Attributes\Authorization\Authorize;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiRequest;
use Core\Contracts\Api\MethodNotAllowed;
use Core\Contracts\Api\Unauthorized;
use Core\Controllers\ApiController;
use Core\Database\Database;
use Core\DependencyInjection\ServiceContainer;
use Core\Enums\HttpMethod;
use JetBrains\PhpStorm\NoReturn;
use ReflectionAttribute;

/**
 * Represents the main Application that will handle the request.
 */
class App
{
    private readonly HttpContext $httpContext;
    /**
     * @var ApiController[]
     */
    private array $controllers = [];

    function __construct(private readonly ServiceContainer $serviceContainer)
    {
        $this->httpContext = $serviceContainer->resolve(HttpContext::class);
        $this->registerController($serviceContainer->resolve(AuthController::class));
        $this->registerController($serviceContainer->resolve(DirectoryController::class));
        $this->registerController($serviceContainer->resolve(FileController::class));
        $this->registerController($serviceContainer->resolve(SessionController::class));
        $this->registerController($serviceContainer->resolve(UserController::class));
        $this->registerController($serviceContainer->resolve(StorageController::class));
    }

    /**
     * Forwards a given request to the corresponding controller that handles the request.
     */
    #[NoReturn]
    public function handle(string $route, string $method): void
    {
        $this->handleRequest($route, $method);
        $this->cleanup();
    }

    private function handleRequest(string $route, string $method): void
    {
        if (empty($route))
        {
            return;
        }

        $controller = $this->findControllerByRoute($route);
        if ($controller == null)
        {
            (new MethodNotAllowed())->write();
            return;
        }

        // Check for authorized controllers
        if (!$this->handleAuthorization($controller))
        {
            (new Unauthorized("Invalid access token"))->write();
            return;
        }

        $controller->handleRequest(new ApiRequest($route, HttpMethod::getFromName($method)), $this->httpContext);
    }

    #[NoReturn]
    private function cleanup(): void
    {
        $database = $this->serviceContainer->resolve(Database::class);
        $database->close();
        exit;
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

    private function handleAuthorization(ApiController $controller): bool
    {
        $authAttributes = $controller->reflection->getAttributes(Authorize::class, ReflectionAttribute::IS_INSTANCEOF);
        if (count($authAttributes) == 0)
        {
            return true;
        }

        $authAttribute = $authAttributes[0]->newInstance();
        if (!($authAttribute instanceof Authorize))
        {
            return true;
        }

        $sessionService = $this->serviceContainer->resolve(SessionServiceInterface::class);
        $authorizationToken = $this->httpContext->authorizationToken;
        if (!$authorizationToken)
        {
            return false;
        }

        return $sessionService->validateSession($authorizationToken->token) === true;
    }
}