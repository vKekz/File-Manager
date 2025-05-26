<?php

namespace Controllers;

use Enums\ParameterType;

readonly class ApiControllerMethodParameter
{
    function __construct(public string $name, public ParameterType $parameterType)
    {
    }
}
