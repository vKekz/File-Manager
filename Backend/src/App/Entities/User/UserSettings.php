<?php

namespace App\Entities\User;

use App\Enums\Theme;

/**
 * Represents the settings each user can have.
 */
class UserSettings
{
    /**
     * Gets the users storage settings.
     */
    public UserStorageSettings $storageSettings;
    /**
     * Gets the theme the user has selected. Will be Light by default.
     */
    public Theme $theme;

    function __construct()
    {
        $this->storageSettings = new UserStorageSettings();
        $this->theme = Theme::Light;
    }

    public function serialize(): string
    {
        return json_encode($this);
    }

    public function equals(UserSettings $other): bool
    {
        return $this->serialize() === $other->serialize();
    }

    public static function fromArray(array $data): UserSettings
    {
        $settings = new UserSettings();
        $settings->storageSettings = UserStorageSettings::fromArray($data["storageSettings"]);
        $settings->theme = Theme::from($data["theme"]);
        return $settings;
    }
}