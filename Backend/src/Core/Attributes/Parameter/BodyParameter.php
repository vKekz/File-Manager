<?php

namespace Core\Attributes\Parameter;

use Attribute;
use Core\Enums\ParameterType;

/**
 * Gets a method argument from the HTTP request body.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParameter extends ParameterAttribute
{
    function __construct()
    {
        parent::__construct(ParameterType::Body);
    }
}