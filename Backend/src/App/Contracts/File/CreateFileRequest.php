<?php

namespace App\Contracts\File;

use Core\Contracts\File\UploadedFile;

class CreateFileRequest
{
    function __construct(public UploadedFile $file, public string $directoryId)
    {
    }
}