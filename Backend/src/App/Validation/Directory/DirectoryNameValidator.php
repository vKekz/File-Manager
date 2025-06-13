<?php

namespace App\Validation\Directory;

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
        if (strpbrk($name, self::getInvalidCharacters()))
        {
            return false;
        }

        $input = $name;

        return true;
    }

    private static function getInvalidCharacters(): string
    {
        return '<>|:*?\/.';
    }

    public static function getInvalidCharactersFormatted(): string
    {
        $invalidCharsArray = str_split(self::getInvalidCharacters());
        return join(" ", $invalidCharsArray);
    }
}