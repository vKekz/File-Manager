<?php

namespace App\Validation\User;

use App\Validation\Validator;

/**
 * @inheritdoc
 */
class EmailValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public static function validate(mixed $input): bool
    {
        // Should match the email standards
        if (strlen($input) > 255 || !filter_var($input, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }

        // Should have a legitimate domain mail server
        $server = substr($input, strpos($input, "@") + 1);
        if (!checkdnsrr($server))
        {
            return false;
        }

        return true;
    }
}