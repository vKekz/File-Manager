<?php

namespace App\Validation\File;

use App\Services\FileSystem\FileSystemHandler;
use App\Validation\Validator;

/**
 * Represents the validator for file names.
 */
class FileNameValidator extends Validator
{
    /**
     * Returns true if the given file name validates correctly.
     *
     * @param string $input
     */
    static function validate(mixed &$input): bool
    {
        // URL friendly encode
        $name = htmlspecialchars($input);

        // Remove any whitespace
        $name = trim($name);

        // Replace characters that cause errors while downloading file
        $name = str_replace([" ", ","], "_", $name);

        // Name cannot have more than 255 characters
        $length = strlen($name);
        if ($length > 255)
        {
            return false;
        }

        // Name cannot contain any special characters
        $fileName = explode(".", $name)[0];
        if (strpbrk($fileName, FileSystemHandler::getInvalidCharacters()))
        {
            return false;
        }

        $input = $name;

        return true;
    }
}