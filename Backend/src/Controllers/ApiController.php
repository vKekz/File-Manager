<?php

namespace Controllers;

/**
 * Represents the base class for an API controller.
 */
abstract class ApiController
{
    /**
     * The route to this controller.
     */
    private string $route;

    function __construct(string $route)
    {
        $this->route = $route;
    }

    public function getRoute(): string
    {
        return $this->route;
    }
}