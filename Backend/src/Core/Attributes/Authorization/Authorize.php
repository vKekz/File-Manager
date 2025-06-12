<?php

namespace Core\Attributes\Authorization;

use Attribute;

/**
 * Marks an ApiController as authorized which means that it will validate the current session and cancel the request if invalid.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Authorize
{
}