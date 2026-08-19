<?php

declare(strict_types=1);

require_once __DIR__ . '/src/Core/Autoloader.php';
require_once __DIR__ . '/src/Helpers/url.php';

use App\Core\Router;
use App\Core\Autoloader;

Autoloader::register();

$router = new Router();

require __DIR__ . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
