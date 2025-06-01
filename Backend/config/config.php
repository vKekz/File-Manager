<?php

declare(strict_types=1);

# Header settings to allow frontend HTTP requests
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Authorization");

# Session settings for better security
// https://www.php.net/manual/en/session.configuration.php#ini.session.sid-length
ini_set("session.sid_length", 192);

// https://www.php.net/manual/en/session.configuration.php#ini.session.sid-bits-per-character
ini_set("session.sid_bits_per_character", 6);

// https://www.php.net/manual/en/session.configuration.php#ini.session.use-strict-mode
ini_set("session.use_strict_mode", 1);

// https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-samesite
ini_set("session.cookie_samesite", 1);

// https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-httponly
ini_set("session.cookie_httponly", 1);

// https://www.php.net/manual/en/session.configuration.php#ini.session.cookie-secure
ini_set("session.cookie_secure", 1);

//error_reporting(0);
//ini_set("display_errors", 0);

require_once realpath("../vendor/autoload.php");