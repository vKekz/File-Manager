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
        // TODO: Status code cancels response in angular, so for now always 200
        header("Content-Type: application/json; charset=utf-8", true, 200);
        echo json_encode($this->statusCode == 200 ? $this->message : $this, JSON_PRETTY_PRINT);
    }
}