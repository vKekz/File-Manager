<?php

namespace Core\Contracts\Api;

class BadRequest extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 400);
    }
}