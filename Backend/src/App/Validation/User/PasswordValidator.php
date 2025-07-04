<?php

namespace App\Validation\User;

use App\Validation\Validator;

/**
 * Represents the validator for user passwords.
 */
class PasswordValidator extends Validator
{
    /**
     * Returns true if the given password validates correctly.
     *
     * @param string $input
     */
    public static function validate(mixed &$input): bool
    {
        // Passwords must be at least 8 characters
        if (strlen($input) < 8)
        {
            return false;
        }

        // Passwords should at least have an uppercase/lowercase letter, number and special character
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d\s])[A-Za-z\d\S]{8,}$/", $input))
        {
            return false;
        }

        return true;
    }
}