<?php

namespace Core\DependencyInjection;

/**
 * Represents the container that resolves a collection of registered services.
 */
interface ServiceContainerInterface
{
    /**
     * Registers a class for the given interface.
     */
    function register(mixed $interface, mixed $class): void;
    /**
     * Returns a resolved class for the given interface.
     */
    function resolve(mixed $interface): mixed;
}