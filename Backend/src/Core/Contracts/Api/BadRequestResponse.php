<?php

namespace Core\Contracts\Api;

class BadRequestResponse extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 400);
    }
}