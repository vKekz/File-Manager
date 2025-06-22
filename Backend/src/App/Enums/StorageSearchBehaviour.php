<?php

namespace App\Enums;

/**
 * Represents the behaviour that is needed when searching a user storage.
 */
enum StorageSearchBehaviour: int
{
    /**
     * The search will contain all existing files and directories.
     */
    case Full = 0;
    /**
     * The search will only contain the files and directories of the current directory.
     */
    case Current = 1;
}