<?php

namespace Core\Attributes\Parameter;

use Attribute;
use Core\Enums\ParameterType;

/**
 * Gets a method argument from the HTTP request files.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class FileParameter extends ParameterAttribute
{
    function __construct(public string $realName = "")
    {
        parent::__construct($realName, ParameterType::File);
    }
}