<?php

namespace Core\Attributes\Parameter;

use Attribute;
use Core\Enums\ParameterType;

/**
 * Gets a method argument from the URL query.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParameter extends ParameterAttribute
{
    function __construct(public string $realName = "")
    {
        parent::__construct($realName, ParameterType::Query);
    }
}