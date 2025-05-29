<?php

namespace Core\Controllers\Context;

/**
 * Represents the context that holds information about the current HTTP request.
 */
class HttpContext
{
    public array $requestHeaders;
    public array $requestQueryParameters;
}