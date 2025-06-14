<?php

namespace App\Contracts\User;

use App\Entities\User\UserSettings;

readonly class UpdateUserSettingsRequest
{
    function __construct(public string $id, public UserSettings $settings)
    {
    }

    public static function deserialize(string $json): UpdateUserSettingsRequest
    {
        $decoded = json_decode($json, true);
        return new self($decoded["id"], UserSettings::fromArray($decoded["settings"]));
    }
}