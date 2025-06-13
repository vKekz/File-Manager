<?php

namespace Core\Contracts\Api;

class FileResponse extends ApiResponse
{
    function __construct(public readonly string $path, public readonly string $name, mixed $message = "")
    {
        parent::__construct($message, 200);
    }

    public function write(): void
    {
        if (!file_exists($this->path) || !is_file($this->path))
        {
            return;
        }

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: filename=" . $this->name);

        flush();
        ob_clean();
        readfile($this->path);
    }
}