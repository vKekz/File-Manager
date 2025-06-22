<?php

namespace App\Enums;

/**
 * Represents the behaviour that is needed when searching a user storage.
 */
enum StorageSearchBehaviour: int
{
    /**
     * Expanded search that will contain all existing files and directories.
     */
    case Expanded = 0;
    /**
     * Classic search will only contain the files and directories of the current directory.
     */
    case Classic = 1;
}