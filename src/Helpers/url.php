<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $config = require __DIR__ . '/../../config/config.php';

    return rtrim($config['app']['base_url'], '/')
        . '/'
        . ltrim($path, '/');
}

function baseUrl(string $path = ''): string
{
    return url($path);
}
