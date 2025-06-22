<?php

namespace App\Validation\Directory;

use App\Services\FileSystem\FileSystemHandler;
use App\Validation\Validator;

/**
 * Represents the validator for directory names.
 */
class DirectoryNameValidator extends Validator
{
    /**
     * Returns true if the given directory name validates correctly.
     *
     * @param string $input
     */
    public static function validate(mixed &$input): bool
    {
        // URL friendly encode
        $name = htmlspecialchars($input);

        // Remove any whitespace
        $name = trim($name);

        // Name cannot be empty or have more than 255 characters
        $length = strlen($name);
        if ($length === 0 || $length > 255)
        {
            return false;
        }

        // Name cannot contain any special characters
        if (strpbrk($name, FileSystemHandler::getInvalidCharacters()))
        {
            return false;
        }

        $input = $name;

        return true;
    }
}