<?php

namespace Attributes\Parameter\Types;

use Attribute;
use Attributes\Parameter\ParameterAttribute;
use Enums\ParameterType;

#[Attribute(Attribute::TARGET_PARAMETER)]
class BodyParameter extends ParameterAttribute
{
    function __construct()
    {
        parent::__construct(ParameterType::Body);
    }
}