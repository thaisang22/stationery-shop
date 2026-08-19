<?php

declare(strict_types=1);

namespace App\Core;
use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }
        ;

        $config = require __DIR__ . '/../../config/config.php';
        $db = $config['database'];

        $dsn = sprintf(
            'mysql:host=%s;port;port=%d;dbname=%s;charset=%s',
            $db['host'],
            $db['port'],
            $db['name'],
            $db['charset'],

        );

        self::$connection = new PDO(
            $dsn,
            $db['username'],
            $db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return self::$connection;
    }
}
