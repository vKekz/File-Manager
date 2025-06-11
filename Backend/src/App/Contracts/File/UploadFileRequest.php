<?php

namespace App\Contracts\File;

use Core\Files\UploadedFile;

class UploadFileRequest
{
    function __construct(public UploadedFile $file, public string $directoryId)
    {
    }
}