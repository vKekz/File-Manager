<?php

namespace App\Validation\User;

use App\Validation\Validator;

/**
 * @inheritdoc
 */
class UsernameValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public static function validate(mixed $input): bool
    {
        $username = htmlspecialchars($input);

        // Usernames should have between 4 and 16 characters
        if (strlen($username) < 4 || strlen($username) > 16)
        {
            return false;
        }

        // Make sure that usernames only contain letters, numbers and underscores
        if (!preg_match("/^[a-zA-Z0-9_]{4,16}$/", $username))
        {
            return false;
        }

        return true;
    }
}