<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->loadEnv(__DIR__ . '/.env');

$dbConfig = require __DIR__ . '/config/database.php';

return [
    'paths' => [
        'migrations' => [
            'App\\Database\\Migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        ],
    ],
    'environments' => [
        'default_migration_table' => 'phinx_migrations',
        'default_environment' => 'default',
        'default' => [
            'adapter' => $dbConfig['driver'],
            'host' => $dbConfig['host'],
            'port' => $dbConfig['port'],
            'name' => $dbConfig['name'],
            'user' => $dbConfig['user'],
            'pass' => $dbConfig['password'],
            'charset' => 'utf8',
        ],
    ],
];
