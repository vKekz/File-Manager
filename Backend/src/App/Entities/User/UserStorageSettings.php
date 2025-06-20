<?php

namespace App\Entities\User;

use App\Enums\FileReplacementMode;

/**
 * Represents settings that determines what details are shown on the storage page.
 */
class UserStorageSettings
{
    /**
     * Gets the method that decides whether existing files should be replaced or kept when uploading a duplicate name.
     *
     * Will be set to replace by default.
     */
    public FileReplacementMode $fileReplacementMode = FileReplacementMode::Replace;
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
        return $settings;
    }
}