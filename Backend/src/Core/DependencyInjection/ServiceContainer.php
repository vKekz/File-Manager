<?php

namespace Core\DependencyInjection;

use ReflectionClass;

/**
 * Represents the container that resolves registered services.
 */
class ServiceContainer
{
    private array $services = [];
    private array $cachedClasses = [];

    /**
     * Registers a class for the given interface.
     */
    function register(mixed $interface, mixed $class = null): void
    {
        $this->services[$interface] = $class ?? $interface;
    }

    /**
     * Returns a resolved class for the given interface.
     */
    function resolve(mixed $interface): mixed
    {
        // Get registered class for interface
        $class = $this->services[$interface];
        if (!class_exists($class))
        {
            return null;
        }

        // Get reflection of class
        $reflection = new ReflectionClass($class);

        // Check if constructor exists
        $constructor = $reflection->getConstructor();
        if ($constructor == null)
        {
            return new $class;
        }

        // Check for parameters in constructor
        $parameters = $constructor->getParameters();
        if (count($parameters) == 0)
        {
            return new $class;
        }

        // Go through each parameter and resolve (will traverse children as well)
        $dependencies = [];
        foreach ($parameters as $parameter)
        {
            $type = $parameter->getType();
            if ($type == null)
            {
                continue;
            }

            $dependencies[] = $this->get($type->getName());
        }

        // Finally return new instance of resolved class with its dependencies
        return new $class(...$dependencies);
    }

    private function get(mixed $interface): mixed
    {
        $resolved = $this->cachedClasses[$interface] ?? $this->resolve($interface);

        // Cache resolved class
        if (!isset($this->cachedClasses[$interface]))
        {
            $this->cachedClasses[$interface] = $resolved;
        }

        return $resolved;
    }
}