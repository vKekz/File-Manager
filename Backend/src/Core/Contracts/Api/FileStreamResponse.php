<?php

namespace Core\Contracts\Api;

class FileStreamResponse extends ApiResponse
{
    function __construct(
        public readonly string $data,
        public readonly string $name,
        public readonly string $size,
        mixed $message = ""
    )
    {
        parent::__construct($message, 200);
    }

    public function write(): void
    {
        if (ob_get_level())
        {
            ob_end_clean();
        }

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$this->name");
        header("Content-Length: $this->size");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Connection: close");

        $output = fopen("php://output", "wb");
        fwrite($output, $this->data);
        fclose($output);
        flush();
    }
}