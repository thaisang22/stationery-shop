<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Core/Autoloader.php';
require_once __DIR__ . '/src/Helpers/url.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Core\Router;
use App\Core\Autoloader;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Autoloader::register();

$router = new Router();

require __DIR__ . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
