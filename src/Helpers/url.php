<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    static $baseUrl = null;
    static $basePath = null;

    if ($baseUrl === null) {
        $config = require __DIR__ . '/../../config/config.php';
        $baseUrl = rtrim((string) ($config['app']['base_url'] ?? ''), '/');
        $basePath = rtrim((string) (parse_url($baseUrl, PHP_URL_PATH) ?? ''), '/');
    }

    if ($path === '' || $path === '/') {
        return $baseUrl !== '' ? $baseUrl : '/';
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // Nếu $path đã chứa sẵn $basePath ở đầu (ví dụ: /stationery/admin), loại bỏ $basePath để tránh bị lặp thành /stationery/stationery/admin
    if ($basePath !== '' && (str_starts_with($path, $basePath . '/') || $path === $basePath)) {
        $path = substr($path, strlen($basePath));
    }

    $cleanPath = ltrim($path, '/');
    if ($cleanPath === '') {
        return $baseUrl !== '' ? $baseUrl : '/';
    }

    return ($baseUrl !== '' ? $baseUrl : '') . '/' . $cleanPath;
}

function baseUrl(string $path = ''): string
{
    return url($path);
}
