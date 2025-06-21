<?php

namespace App\Contracts\File;

use Core\Contracts\File\UploadedFile;

class UploadFileRequest
{
    function __construct(public UploadedFile $file, public string $directoryId)
    {
    }
}