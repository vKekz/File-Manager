<?php

namespace Core\Controllers;

use Core\Attributes\Http\HttpAttribute;
use Core\Attributes\Parameter\ParameterAttribute;
use Core\Context\HttpContext;
use Core\Contracts\Api\ApiRequest;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequest;
use Core\Enums\ParameterType;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    private array $routes = [];

    function __construct(public readonly string $endpoint)
    {
        $this->registerRoutes();
    }

    /**
     * Attempts to find a method matching our requested route, calls it and returns the response in JSON format.
     */
    public function handleRequest(ApiRequest $request, HttpContext $httpContext): void
    {
        $method = $this->findControllerMethodByRequest($request);
        if ($method == null)
        {
            // Method not allowed if route does not exist
            http_response_code(405);
            return;
        }

        $arguments = $this->tryFindRequestArguments($method, $httpContext);
        if ($arguments instanceof ApiResponse)
        {
            $arguments->write();
            return;
        }

        // Finally call method and write response
        $response = $method->call(...$arguments);
        $response->write();
    }

    private function findControllerMethodByRequest(ApiRequest $request): ?ApiControllerMethod
    {
        $requestRoute = $request->route;
        $requestMethod = $request->method;

        if (!array_key_exists($requestMethod->name, $this->routes))
        {
            return null;
        }

        $methodDictionary = $this->routes[$requestMethod->name];
        if (!array_key_exists($requestRoute, $methodDictionary))
        {
            return null;
        }

        return $methodDictionary[$requestRoute];
    }

    private function registerRoutes(): void
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods();

        // Go through each method of the controller
        foreach ($methods as $method)
        {
            // Check for HTTP attributes
            $attributes = $method->getAttributes(HttpAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
            if (count($attributes) == 0)
            {
                continue;
            }

            $httpAttribute = $attributes[0]->newInstance();
            if (!($httpAttribute instanceof HttpAttribute))
            {
                continue;
            }

            // Register route as a child of the HTTP method
            // e.g. dict[GET][route] = method
            $parameters = $this->findMethodParameters($method);
            $route = $this->endpoint . $httpAttribute->route;
            $this->routes[$httpAttribute->method->name][$route] = new ApiControllerMethod($this, $method->name, $parameters);
        }
    }

    /**
     * Returns an array of parameters for the given controller method.
     */
    private function findMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];
        foreach ($method->getParameters() as $parameter)
        {
            // Check for the parameter attribute
            $parameterAttributes = $parameter->getAttributes(ParameterAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
            if (count($parameterAttributes) == 0)
            {
                continue;
            }

            $parameterAttribute = $parameterAttributes[0]->newInstance();
            if (!($parameterAttribute instanceof ParameterAttribute))
            {
                continue;
            }

            // Parameters might be named differently than in the incoming request
            $name = empty($parameterAttribute->realName) ? $parameter->getName() : $parameterAttribute->realName;
            $parameters[] = new ApiControllerMethodParameter($name, $parameterAttribute->type);
        }

        return $parameters;
    }

    /**
     * Attempts to find the arguments for the calling controller method from the HTTP request. Returns status 400 on failure.
     */
    private function tryFindRequestArguments(ApiControllerMethod $method, HttpContext $httpContext): array | BadRequest
    {
        $arguments = [];
        foreach ($method->parameters as $parameter)
        {
            $name = $parameter->name;
            switch ($parameter->type)
            {
                case ParameterType::Body:
                    $requestBody = file_get_contents("php://input");
                    if (!$requestBody)
                    {
                        return new BadRequest("Request body is missing");
                    }

                    $arguments[] = $requestBody;
                    break;
                case ParameterType::Query:
                    $queryParameters = $httpContext->requestQueryParameters;
                    if (!array_key_exists($name, $queryParameters))
                    {
                        return new BadRequest("Query parameter $name is missing");
                    }

                    $arguments[] = $queryParameters[$name];
                    break;
                case ParameterType::Header:
                    $requestHeaders = $httpContext->requestHeaders;
                    if (!array_key_exists($name, $requestHeaders))
                    {
                        return new BadRequest("Header $name is missing");
                    }

                    $arguments[] = $requestHeaders[$name];
                    break;
            }
        }

        return $arguments;
    }
}