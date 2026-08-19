<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(
        string $method,
        string $path,
        array $handler
    ): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(
        string $method,
        string $requestUri
    ): void {
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

        $baseUrl = rtrim(
            (require __DIR__ . '/../../config/config.php')['app']['base_url'],
            '/'
        );

        if ($baseUrl !== '' && str_starts_with($path, $baseUrl)) {
            $path = substr($path, strlen($baseUrl));
        }

        $path = '/' . trim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {
            if (
                $route['method'] === $method &&
                $route['path'] === $path
            ) {
                $this->callHandler($route['handler']);
                return;
            }
        }

        http_response_code(404);

        $controller = new \App\Controllers\ErrorController();
        $controller->notFound();
    }

    private function callHandler(array $handler): void
    {
        [$controllerClass, $method] = $handler;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException(
                "Controller {$controllerClass} not found."
            );
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new RuntimeException(
                "Method {$method} not found."
            );
        }

        $controller->{$method}();
    }
}
