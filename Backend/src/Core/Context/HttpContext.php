<?php

namespace Core\Context;

use App\Services\Session\Token\Payload;
use Core\Authorization\AuthorizationToken;

/**
 * Represents the context that holds information about the current HTTP request.
 */
class HttpContext
{
    public readonly array $requestHeaders;
    public readonly array $requestPostParameters;
    public readonly array $requestQueryParameters;
    public readonly array $requestUploadedFiles;
    public readonly string $requestUserAgent;
    public readonly string $requestAddress;

    public readonly ?AuthorizationToken $authorizationToken;
    public ?Payload $payload;

    function __construct()
    {
        $this->requestHeaders = getallheaders();
        $this->requestPostParameters = $_POST;
        $this->requestQueryParameters = $_GET;
        $this->requestUploadedFiles = $_FILES;
        $this->requestUserAgent = $_SERVER["HTTP_USER_AGENT"];
        $this->requestAddress = $_SERVER["REMOTE_ADDR"];
        $this->authorizationToken = AuthorizationToken::fromHeader($this->requestHeaders["Authorization"] ?? null);
    }
}