<?php

namespace App\Entities\User;

use App\Enums\FileReplacementBehaviour;
use App\Enums\StorageSearchBehaviour;

/**
 * Represents settings that determines what details are shown on the storage page.
 */
class UserStorageSettings
{
    /**
     * Gets the behaviour that decides whether existing files should be replaced or kept when uploading a duplicate.
     *
     * Will be set to replace by default.
     */
    public FileReplacementBehaviour $fileReplacementBehaviour = FileReplacementBehaviour::Replace;
    /**
     * Gets the behaviour that decides which files and directories are contained in the search.
     *
     * Will be set to current by default.
     */
    public StorageSearchBehaviour $storageSearchBehaviour = StorageSearchBehaviour::Current;
    /**
     * A value indicating whether file hashes are shown on the storage page.
     *
     * Will be set to false by default.
     */
    public bool $showFileHash = false;
    /**
     * A value indicating whether file upload dates are shown on the storage page.
     *
     * Will be set to false by default.
     */
    public bool $showUploadDate = false;

    public static function fromArray(array $data): UserStorageSettings
    {
        $settings = new UserStorageSettings();
        $settings->showFileHash = $data["showFileHash"];
        $settings->showUploadDate = $data["showUploadDate"];
        $settings->fileReplacementBehaviour = FileReplacementBehaviour::from($data["fileReplacementBehaviour"]);
        $settings->storageSearchBehaviour = StorageSearchBehaviour::from($data["storageSearchBehaviour"]);
        return $settings;
    }
}