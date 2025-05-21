<?php

namespace Controllers\Contracts\Api;

/**
 * Represents an API response.
 */
abstract class ApiResponse
{
    private mixed $data;
    private int $statusCode;

    function __construct(mixed $data, int $statusCode)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}