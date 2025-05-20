<?php

namespace Attributes\Http;

/**
 * Represents the accepting HTTP methods.
 */
enum HttpMethod
{
    /**
     * Represents the HTTP GET method.
     */
    case Get;
    /**
     * Represents the HTTP POST method.
     */
    case Post;
    /**
     * Represents the HTTP DELETE method.
     */
    case Delete;
    /**
     * Represents the HTTP PATCH method.
     */
    case Patch;

    /**
     * Returns an HTTP method based on its name, otherwise null if not found.
     */
    public static function getFromName(string $name): ?HttpMethod
    {
        foreach (self::cases() as $method)
        {
            if (strcmp(strtoupper($method->name), $name) == 0)
            {
                return $method;
            }
        }

        return null;
    }
}