<?php

namespace App\Validation;

/**
 * Represents the validator base class.
 */
abstract class Validator
{
    /**
     * Returns true if the given input validates correctly.
     */
    public abstract static function validate(mixed $input): bool;
}