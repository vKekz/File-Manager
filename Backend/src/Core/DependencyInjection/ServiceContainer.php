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
    public function register(mixed $interface, mixed $class): void
    {
        $this->services[$interface] = $class;
    }

    /**
     * Registers a class as a singleton.
     */
    public function addSingleton(mixed $class): void
    {
        $this->services[$class] = $class;
    }

    /**
     * Returns a resolved class for the given interface.
     */
    public function resolve(mixed $interface): mixed
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
            return $this->get($interface, false);
        }

        // Check for parameters in constructor
        $parameters = $constructor->getParameters();
        if (count($parameters) == 0)
        {
            return $this->get($interface, false);
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

    private function get(mixed $interface, bool $resolve = true): mixed
    {
        // This was needed because services/classes that don't have a constructor
        // were not cached correctly but rather instantiated multiple times
        if (!$resolve)
        {
            $className = $this->services[$interface];
            $classInstance = new $className();
            $this->cacheService($interface, $classInstance);

            return $classInstance;
        }

        $resolved = $this->cachedClasses[$interface] ?? $this->resolve($interface);
        $this->cacheService($interface, $resolved);

        return $resolved;
    }

    private function cacheService(mixed $interface, mixed $resolved): void
    {
        if (isset($this->cachedClasses[$interface]))
        {
            return;
        }

        $this->cachedClasses[$interface] = $resolved;
    }
}