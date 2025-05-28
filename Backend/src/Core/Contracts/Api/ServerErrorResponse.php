<?php

namespace Core\Contracts\Api;

class ServerErrorResponse extends ApiResponse
{
    function __construct(mixed $data = "")
    {
        parent::__construct($data, 500);
    }
}