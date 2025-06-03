<?php

namespace Core\Context;

use Core\Authorization\AuthorizationToken;

/**
 * Represents the context that holds information about the current HTTP request.
 */
class HttpContext
{
    public array $requestHeaders;
    public array $requestQueryParameters;
    public string $requestUserAgent;
    public string $requestAddress;
    public ?AuthorizationToken $authorizationToken;

    function __construct()
    {
        $this->requestHeaders = getallheaders();
        $this->requestQueryParameters = $_GET;
        $this->requestUserAgent = $_SERVER["HTTP_USER_AGENT"];
        $this->requestAddress = $_SERVER["REMOTE_ADDR"];
        $this->authorizationToken = AuthorizationToken::fromHeader($this->requestHeaders["Authorization"] ?? null);
    }
}