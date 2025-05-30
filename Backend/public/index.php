<?php

require_once "../config/config.php";

use App\App;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Hash\HashService;
use App\Services\Hash\HashServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use Core\Database\Database;
use Core\DependencyInjection\ServiceContainer;

// Register services
$serviceContainer = new ServiceContainer();
$serviceContainer->register(Database::class, Database::class);
$serviceContainer->register(HashServiceInterface::class, HashService::class);
$serviceContainer->register(UserRepositoryInterface::class, UserRepository::class);
$serviceContainer->register(UserServiceInterface::class, UserService::class);

// Run app
$app = new App($serviceContainer);
$app->handleRequest($_GET["route"], $_SERVER["REQUEST_METHOD"]);