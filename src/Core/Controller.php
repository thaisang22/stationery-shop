<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\LayoutController;

abstract class Controller
{
    protected function view(
        string $view,
        array $data = []
    ): void {
        $layoutController = new LayoutController();
        $layoutData = $layoutController->getHeaderData();
        $data = array_merge(
            $layoutData,
            $data
        );

        extract($data);

        $viewFile = __DIR__
            . '/../Views/'
            . $view
            . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException(
                "View {$view} not found."
            );
        }

        require $viewFile;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}