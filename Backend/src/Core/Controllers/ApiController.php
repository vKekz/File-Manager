<?php

namespace Core\Controllers;

use Core\Attributes\Http\HttpAttribute;
use Core\Attributes\Parameter\ParameterAttribute;
use Core\Contracts\Api\ApiRequest;
use Core\Contracts\Api\ApiResponse;
use Core\Contracts\Api\BadRequestResponse;
use Core\Controllers\Context\HttpContext;
use Core\Enums\ParameterType;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    private readonly HttpContext $httpContext;
    private readonly ReflectionClass $reflection;
    private array $routes = [];

    function __construct(public readonly string $endpoint)
    {
        $this->httpContext = new HttpContext();
        $this->reflection = new ReflectionClass($this);

        $this->registerRoutes();
    }

    /**
     * Attempts to find a method matching our requested route, calls it and returns the response in JSON format.
     */
    public function handleRequest(ApiRequest $request): void
    {
        $method = $this->findControllerMethodByRequest($request);
        if ($method == null)
        {
            http_response_code(405);
            return;
        }

        // Fill context properties
        $headers = getallheaders();
        $this->httpContext->requestHeaders = $headers;
        $this->httpContext->requestQueryParameters = $_GET;

        $arguments = $this->tryFindRequestArguments($method);
        if ($arguments instanceof ApiResponse)
        {
            $arguments->write();
            return;
        }

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
        $methods = $this->reflection->getMethods();
        foreach ($methods as $method)
        {
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

            $parameters = $this->findMethodParameters($method);
            $route = $this->endpoint . $httpAttribute->route;
            $this->routes[$httpAttribute->method->name][$route] = new ApiControllerMethod($this, $method->name, $parameters);
        }
    }

    private function findMethodParameters(ReflectionMethod $method): array
    {
        $parameters = [];
        foreach ($method->getParameters() as $parameter)
        {
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

            $name = empty($parameterAttribute->realName) ? $parameter->getName() : $parameterAttribute->realName;
            $parameters[] = new ApiControllerMethodParameter($name, $parameterAttribute->type);
        }

        return $parameters;
    }

    private function tryFindRequestArguments(ApiControllerMethod $method): array | ApiResponse
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
                        return new BadRequestResponse("Request body is missing");
                    }

                    $arguments[] = $requestBody;
                    break;
                case ParameterType::Query:
                    $queryParameters = $this->httpContext->requestQueryParameters;
                    if (!array_key_exists($name, $queryParameters))
                    {
                        return new BadRequestResponse("Query parameter $name is missing");
                    }

                    $arguments[] = $queryParameters[$name];
                    break;
                case ParameterType::Header:
                    $requestHeaders = $this->httpContext->requestHeaders;
                    if (!array_key_exists($name, $requestHeaders))
                    {
                        return new BadRequestResponse("Header $name is missing");
                    }

                    $arguments[] = $requestHeaders[$name];
                    break;
            }
        }

        return $arguments;
    }
}