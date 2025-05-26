<?php

namespace Controllers;

use Attributes\Http\HttpMethodAttribute;
use Attributes\Parameter\ParameterAttribute;
use Contracts\Api\ApiRequest;
use Enums\ParameterType;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    private readonly ReflectionClass $reflection;
    private array $routes = [];

    function __construct(public readonly string $endpoint)
    {
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
            http_response_code(500);
            return;
        }

        $arguments = [];
        foreach ($method->parameters as $parameter)
        {
            switch ($parameter->parameterType)
            {
                case ParameterType::Body:
                    $arguments[] = file_get_contents("php://input");
                break;
                case ParameterType::Query:
                    $arguments[] = $_GET[$parameter->name];
                break;
            }
        }

        // https://wiki.php.net/rfc/argument_unpacking
        $response = $method->call(...$arguments);
        header("Content-Type: application/json; charset=utf-8", true, $response->statusCode);

        echo json_encode($response->data, JSON_PRETTY_PRINT);
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
            $attributes = $method->getAttributes(HttpMethodAttribute::class, ReflectionAttribute::IS_INSTANCEOF);
            if (count($attributes) == 0)
            {
                continue;
            }

            $httpAttribute = $attributes[0]->newInstance();
            if (!($httpAttribute instanceof HttpMethodAttribute))
            {
                continue;
            }

            $parameters = $this->getParametersForMethod($method);
            $route = $this->endpoint . $httpAttribute->route;
            $this->routes[$httpAttribute->method->name][$route] = new ApiControllerMethod($this, $method->name, $parameters);
        }
    }

    private function getParametersForMethod(ReflectionMethod $method): array
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

            $parameters[] = new ApiControllerMethodParameter($parameter->getName(), $parameterAttribute->parameterType);
        }

        return $parameters;
    }
}