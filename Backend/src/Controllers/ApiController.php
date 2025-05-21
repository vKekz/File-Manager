<?php

namespace Controllers;

use Attributes\Http\HttpMethodAttribute;
use Controllers\Contracts\Api\ApiRequest;
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
     * Attempts to find a method matching our requested route, calls it and returns the response in JSON format.
     */
    public function handleRequest(ApiRequest $request): void
    {
        $method = $this->findControllerMethodByRequest($request);
        if ($method == null)
        {
            http_response_code(500);
            return;
        }

        $response = $method->call();
        header("Content-Type: application/json; charset=utf-8", true, $response->getStatusCode());

        echo json_encode($response->getData(), JSON_PRETTY_PRINT);
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    private function findControllerMethodByRequest(ApiRequest $request): ?ApiControllerMethod
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
                // Skip since we only support one attribute
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
                continue;
            }

            // TODO: Arguments, Request body/parameters
            return new ApiControllerMethod($method->name, $this);
        }

        return null;
    }
}