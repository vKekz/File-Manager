<?php

namespace Core\Controllers;

use Core\Enums\ParameterType;

readonly class ApiControllerMethodParameter
{
    function __construct(public string $name, public ParameterType $type)
    {
    }
}