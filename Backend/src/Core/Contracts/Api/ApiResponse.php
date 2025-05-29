<?php

namespace Core\Contracts\Api;

/**
 * Represents an API response.
 */
abstract class ApiResponse
{
    function __construct(public mixed $message, public int $statusCode)
    {
    }

    public function write(): void
    {
        header("Content-Type: application/json; charset=utf-8", true, $this->statusCode);
        echo json_encode($this->message, JSON_PRETTY_PRINT);
    }
}