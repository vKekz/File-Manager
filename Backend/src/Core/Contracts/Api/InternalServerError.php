<?php

namespace Core\Contracts\Api;

class InternalServerError extends ApiResponse
{
    function __construct(mixed $message = "")
    {
        parent::__construct($message, 500);
    }
}