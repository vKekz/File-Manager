<?php

namespace Attributes\Parameter;

use Enums\ParameterType;

abstract class ParameterAttribute
{
    function __construct(public readonly ParameterType $parameterType)
    {
    }
}