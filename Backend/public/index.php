<?php

require_once "../config/config.php";

use App\App;
use App\Controllers\User\UserController;
use App\Repositories\Session\SessionRepository;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Hash\HashService;
use App\Services\Hash\HashServiceInterface;
use App\Services\Session\SessionService;
use App\Services\Session\SessionServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use Core\Context\HttpContext;
use Core\Database\Database;
use Core\DependencyInjection\ServiceContainer;
use Core\Environment\Environment;

// Register singletons
$serviceContainer = new ServiceContainer();
$serviceContainer->register(Environment::class);
$serviceContainer->register(HttpContext::class);
$serviceContainer->register(Database::class);
$serviceContainer->register(HashServiceInterface::class, HashService::class);
$serviceContainer->register(SessionRepositoryInterface::class, SessionRepository::class);
$serviceContainer->register(SessionServiceInterface::class, SessionService::class);
$serviceContainer->register(UserRepositoryInterface::class, UserRepository::class);
$serviceContainer->register(UserServiceInterface::class, UserService::class);
$serviceContainer->register(UserController::class);

// Run app
$app = new App($serviceContainer);
$app->handleRequest($_GET["route"], $_SERVER["REQUEST_METHOD"]);