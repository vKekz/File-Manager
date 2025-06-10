<?php

namespace Core\Contracts\Api;

class InternalServerError extends ApiResponse
{
    function __construct(mixed $message = "Unexpected server error")
    {
        parent::__construct($message, 500);
    }
}