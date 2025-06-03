<?php

namespace Core\Configuration;

/**
 * Represents a set of core configuration properties.
 */
class Configuration
{
    /**
     * Gets the URL for the frontend app.
     */
    public string $frontendUrl;
    /**
     * Gets the name of the environment file.
     * Will be set to ".env" by default.
     */
    public string $environmentFile;

    function __construct()
    {
        $this->environmentFile = ".env";
        $this->load();
    }

    private function load(): void
    {
        $properties = array_keys(get_class_vars(Configuration::class));
        $file = file_get_contents(dirname(__DIR__, 3) . "/config/config.json");
        $json = json_decode($file, true);

        foreach ($properties as $property)
        {
            if (!isset($json[$property]))
            {
                continue;
            }

            $this->{$property} = $json[$property];
        }

        $this->configureHeaders();
        $this->configureSessions();
        $this->configureDateTime();
    }

    private function configureHeaders(): void
    {
        header("Access-Control-Allow-Origin: $this->frontendUrl");
        header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE');
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    private function configureSessions(): void
    {
        // https://www.php.net/manual/en/session.configuration.php#ini.session.use-strict-mode
        ini_set("session.use_strict_mode", 1);

        // https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-samesite
        ini_set("session.cookie_samesite", "Strict");

        // https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
        ini_set("session.cookie_httponly", 1);

        // https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-secure
        ini_set("session.cookie_secure", 1);
    }

    private function configureDateTime(): void
    {
        date_default_timezone_set("UTC");
    }
}