<?php

namespace Core\Contracts\Api;

class FileResponse extends ApiResponse
{
    function __construct(
        public readonly string $path,
        public readonly string $name,
        public readonly string $size,
        mixed $message = ""
    )
    {
        parent::__construct($message, 200);
    }

    /**
     * @see https://www.php.net/manual/en/function.readfile.php#refsect1-function.readfile-examples
     */
    public function write(): void
    {
        if (!file_exists($this->path) || !is_file($this->path))
        {
            return;
        }

        if (ob_get_level())
        {
            ob_end_clean();
        }

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$this->name");
        header("Content-Length: $this->size");
        header("Connection: close");

        readfile($this->path);
        exit;
    }
}