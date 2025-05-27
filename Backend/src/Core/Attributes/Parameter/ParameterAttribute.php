<?php

namespace Core\Attributes\Parameter;

use Core\Enums\ParameterType;

/**
 * Represents the base class for a parameter attribute.
 */
abstract class ParameterAttribute
{
    function __construct(public readonly ParameterType $parameterType)
    {
    }
}