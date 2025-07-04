<?php

namespace App\Validation\User;

use App\Validation\Validator;

/**
 * Represents the validator for usernames.
 */
class UsernameValidator extends Validator
{
    /**
     * Returns true if the given username validates correctly.
     *
     * @param string $input
     */
    public static function validate(mixed &$input): bool
    {
        $username = htmlspecialchars($input);

        // Usernames should have between 4 and 16 characters
        $length = strlen($username);
        if ($length < 4 || $length > 16)
        {
            return false;
        }

        // Make sure that usernames only contain letters, numbers and underscores
        if (!preg_match("/^[a-zA-Z0-9_]{4,16}$/", $username))
        {
            return false;
        }

        $input = $username;

        return true;
    }
}