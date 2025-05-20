<?php

namespace Controllers;

use Attributes\Http\HttpMethodAttribute;
use Controllers\Contracts\ApiRequest;
use Controllers\Contracts\ControllerMethod;
use ReflectionClass;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    /**
     * The route to this controller.
     */
    private readonly string $route;

    /**
     * The reflection details to this controller.
     */
    private readonly ReflectionClass $reflection;

    function __construct(string $route)
    {
        $this->route = $route;
        $this->reflection = new ReflectionClass($this);
    }

    /**
     * Attempts to find a method matching our requested route and calls it.
     */
    public function handleRequest(ApiRequest $request): void
    {
        $method = $this->findControllerMethodByRequest($request);
        if ($method == null)
        {
            return;
        }

        $method->call();
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    private function findControllerMethodByRequest(ApiRequest $request): ?ControllerMethod
    {
        $requestRoute = $request->getRoute();
        $requestMethod = $request->getMethod();

        // Goes through each method the controller has
        $methods = $this->reflection->getMethods();
        foreach ($methods as $method)
        {
            $attributes = $method->getAttributes();
            if (count($attributes) < 1)
            {
                // Skip, since we only support one attribute
                continue;
            }

            // Check if the attribute is our HTTP attribute
            $httpAttribute = $attributes[0]->newInstance();
            if (!($httpAttribute instanceof HttpMethodAttribute))
            {
                continue;
            }

            // Check if the route matches
            $mergedRoute = $this->getRoute() . "/" . $httpAttribute->getRoute();
            if (strcmp($mergedRoute, $requestRoute) != 0 || $httpAttribute->getMethod() != $requestMethod)
            {
                // TODO: MethodNotSupported exception 500 probably
                continue;
            }

             // TODO: Arguments, Request body/parameters
            return new ControllerMethod($method->name, $this);
        }

        return null;
    }
}