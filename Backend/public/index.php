<?php

require_once "bootstrap.php";

use App\App;
use App\Controllers\Auth\AuthController;
use App\Repositories\Session\SessionRepository;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Session\SessionService;
use App\Services\Session\SessionServiceInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use Core\Configuration\Configuration;
use Core\Context\HttpContext;
use Core\Database\Database;
use Core\DependencyInjection\ServiceContainer;
use Core\Environment\Environment;

// Register services
$serviceContainer = new ServiceContainer();
$serviceContainer->addSingleton(Configuration::class);
$serviceContainer->addSingleton(Environment::class);
$serviceContainer->addSingleton(HttpContext::class);
$serviceContainer->addSingleton(Database::class);
$serviceContainer->register(CryptographicServiceInterface::class, CryptographicService::class);
$serviceContainer->register(SessionRepositoryInterface::class, SessionRepository::class);
$serviceContainer->register(SessionServiceInterface::class, SessionService::class);
$serviceContainer->register(UserRepositoryInterface::class, UserRepository::class);
$serviceContainer->register(AuthServiceInterface::class, AuthService::class);
$serviceContainer->addSingleton(AuthController::class);

// Run app
$app = new App($serviceContainer);
$app->handleRequest($_GET["route"], $_SERVER["REQUEST_METHOD"]);