<?php

namespace Core\Environment;

use Core\Configuration\Configuration;
use Core\Enums\EnvironmentKey;

/**
 * Represents the class that holds environment variables loaded from the .env file in the root directory.
 */
class Environment
{
    private array $variables = [];

    function __construct(private readonly Configuration $configuration)
    {
        $this->load();
    }

    /**
     * Attempts to find the value for the given environment key, otherwise returns an empty string.
     */
    public function get(EnvironmentKey $key): string
    {
        return $this->variables[$key->value] ?? "";
    }

    private function load(): void
    {
        $file = file_get_contents(dirname(__DIR__, 3) . "/" . $this->configuration->environmentFile);
        $lines = explode(PHP_EOL, $file);
        foreach ($lines as $line)
        {
            // Skip empty lines
            if (empty(trim($line)))
            {
                continue;
            }

            // Skip comments
            if (str_starts_with($line, "#"))
            {
                continue;
            }

            [$key, $value] = explode("=", $line, 2);
            $this->variables[$key] = $value;
        }
    }
}