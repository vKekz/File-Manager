<?php

namespace Core\Enums;

/**
 * Represents the available environment variable keys.
 */
enum EnvironmentKey: string
{
    case DB_HOST = "DB_HOST";
    case DB_USER = "DB_USER";
    case DB_PASSWORD = "DB_PASSWORD";
    case DB_NAME = "DB_NAME";
    case HASH_MASTER_KEY = "HASH_MASTER_KEY";
    case ENCRYPTION_MASTER_KEY = "ENCRYPTION_MASTER_KEY";
}