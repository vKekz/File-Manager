<?php

namespace App\Enums;

/**
 * Represents the behaviour when a user uploads a file with the same name already existing.
 */
enum FileReplacementBehaviour: int
{
    /**
     * The existing file will be fully replaced by the newly uploaded one.
     */
    case Replace = 0;
    /**
     * The existing file will be kept by saving the newly uploaded file as a separate entry with an identifier in the name.
     */
    case Keep = 1;
}