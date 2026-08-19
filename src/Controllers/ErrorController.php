<?php

declare(strict_types=1);

namespace App\Controllers;

class ErrorController
{
    public function notFound(): void
    {
        require __DIR__ . '/../Views/errors/404.php';
    }
}
