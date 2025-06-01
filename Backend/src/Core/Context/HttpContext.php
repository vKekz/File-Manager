<?php

namespace Core\Context;

/**
 * Represents the context that holds information about the current HTTP request.
 */
class HttpContext
{
    public array $requestHeaders;
    public array $requestQueryParameters;
    public string $requestUserAgent;
    public string $requestAddress;

    function __construct()
    {
        $this->requestHeaders = getallheaders();
        $this->requestQueryParameters = $_GET;
        $this->requestUserAgent = $_SERVER["HTTP_USER_AGENT"];
        $this->requestAddress = $_SERVER["REMOTE_ADDR"];
    }
}