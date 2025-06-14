<?php

namespace App\Entities\User;

/**
 * Represents settings that determines what details are shown on the storage page.
 */
class UserStorageSettings
{
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