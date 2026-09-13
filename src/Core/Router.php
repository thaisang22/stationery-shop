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

        $config = require __DIR__ . '/../../config/config.php';

        $baseUrl = rtrim(
            $config['app']['base_url'],
            '/'
        );

        /*
         * /git-feature-index-home/product/abc
         *        ↓
         * /product/abc
         */
        if (
            $baseUrl !== '' &&
            str_starts_with($path, $baseUrl)
        ) {
            $path = substr(
                $path,
                strlen($baseUrl)
            );
        }

        $path = '/' . trim($path, '/');

        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        foreach ($this->routes as $route) {

            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute(
                $route['path'],
                $path
            );

            if ($params === false) {
                continue;
            }

            $this->callHandler(
                $route['handler'],
                $params
            );

            return;
        }

        http_response_code(404);

        $controller = new \App\Controllers\ErrorController();

        $controller->notFound();
    }

    private function matchRoute(
        string $routePath,
        string $requestPath
    ): array|false {

        /*
         *
         * {id}
         * {slug}
         * {category}
         */
        $pattern = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '(?P<$1>[^/]+)',
            $routePath
        );

        if ($pattern === null) {
            return false;
        }

        $pattern = '#^' . $pattern . '$#';

        if (
            preg_match(
                $pattern,
                $requestPath,
                $matches
            ) !== 1
        ) {
            return false;
        }

        $params = [];

        foreach ($matches as $key => $value) {

            if (is_string($key)) {
                $params[$key] = urldecode($value);
            }
        }

        return $params;
    }

    private function callHandler(
        array $handler,
        array $params = []
    ): void {

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

        $controller->{$method}(...array_values($params));
    }
}