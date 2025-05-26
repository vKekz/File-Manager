<?php

namespace Controllers;

use Attributes\Http\HttpMethodAttribute;
use Attributes\Parameter\ParameterAttribute;
use Contracts\Api\ApiRequest;
use Enums\ParameterType;
use ReflectionAttribute;
use ReflectionClass;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    private readonly ReflectionClass $reflection;
    private array $routes = [];

    function __construct(public readonly string $route)
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

        $parameters = [];
        foreach ($method->parameters as $parameter) {
            switch ($parameter->parameterType) {
                case ParameterType::Body:
                    $parameters[] = file_get_contents("php://input");
                break;
                case ParameterType::Query:
                    $parameters[] = $_GET[$parameter->name];
                break;
            }
        }

//        var_dump($parameters);

        // TODO: Parameters is passed as an array...
        $response = $method->call($parameters);
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

            $route = $this->route . $httpAttribute->route;
            $this->routes[$httpAttribute->method->name][$route] = new ApiControllerMethod($this, $method->name, $parameters);
        }
    }
}