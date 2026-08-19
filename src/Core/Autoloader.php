<?php

declare(strict_types=1);

namespace App\Core;

class Autoloader
{
    
public static function register(): void 
    {
        spl_autoload_register(function (string $class): void  {
            $prefix = 'App\\';

            if(!str_starts_with($class, $prefix)) {
                return;
            }
            
            //Bỏ App\
            $relativeClass = substr(
                $class,
                strlen($prefix)
            );

            $relativepPath = str_replace(
                '\\',
                '/',
                $relativeClass
            );

            // create link file
            $file = __DIR__ . '/../' . $relativepPath . '.php';

            if (file_exists($file)) 
            {
                require_once $file;
            }

        });
    }
}