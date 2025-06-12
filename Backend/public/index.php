<?php

require_once "bootstrap.php";

use App\App;
use App\Controllers\Auth\AuthController;
use App\Controllers\Directory\DirectoryController;
use App\Controllers\File\FileController;
use App\Controllers\Session\SessionController;
use App\Mapping\Directory\DirectoryMapper;
use App\Mapping\File\FileMapper;
use App\Repositories\Directory\DirectoryRepository;
use App\Repositories\Directory\DirectoryRepositoryInterface;
use App\Repositories\File\FileRepository;
use App\Repositories\File\FileRepositoryInterface;
use App\Repositories\Session\SessionRepository;
use App\Repositories\Session\SessionRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Cryptographic\CryptographicService;
use App\Services\Cryptographic\CryptographicServiceInterface;
use App\Services\Directory\DirectoryService;
use App\Services\Directory\DirectoryServiceInterface;
use App\Services\File\FileService;
use App\Services\File\FileServiceInterface;
use App\Services\FileSystem\FileSystemHandler;
use App\Services\FileSystem\FileSystemHandlerInterface;
use App\Services\Session\SessionService;
use App\Services\Session\SessionServiceInterface;
use App\Services\Token\TokenHandler;
use App\Services\Token\TokenHandlerInterface;
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
$serviceContainer->addSingleton(DirectoryMapper::class);
$serviceContainer->addSingleton(FileMapper::class);

$serviceContainer->register(CryptographicServiceInterface::class, CryptographicService::class);
$serviceContainer->register(SessionRepositoryInterface::class, SessionRepository::class);
$serviceContainer->register(SessionServiceInterface::class, SessionService::class);
$serviceContainer->register(UserRepositoryInterface::class, UserRepository::class);
$serviceContainer->register(AuthServiceInterface::class, AuthService::class);
$serviceContainer->register(TokenHandlerInterface::class, TokenHandler::class);
$serviceContainer->register(DirectoryRepositoryInterface::class, DirectoryRepository::class);
$serviceContainer->register(DirectoryServiceInterface::class, DirectoryService::class);
$serviceContainer->register(FileSystemHandlerInterface::class, FileSystemHandler::class);
$serviceContainer->register(FileRepositoryInterface::class, FileRepository::class);
$serviceContainer->register(FileServiceInterface::class, FileService::class);

$serviceContainer->addSingleton(AuthController::class);
$serviceContainer->addSingleton(DirectoryController::class);
$serviceContainer->addSingleton(FileController::class);
$serviceContainer->addSingleton(SessionController::class);

// Run app
$app = new App($serviceContainer);
$app->handle($_GET["route"], $_SERVER["REQUEST_METHOD"]);