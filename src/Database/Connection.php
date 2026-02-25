<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\DatabaseConfiguration;
use Symfony\Component\Config\Definition\Processor;

class Connection
{
    private static ?\PDO $sharedInstance = null;

    public static function setSharedInstance(?\PDO $pdo): void
    {
        self::$sharedInstance = $pdo;
    }

    /**
     * @param array<string, mixed> $rawConfig
     */
    public static function createFromConfig(array $rawConfig): \PDO
    {
        if (self::$sharedInstance !== null) {
            return self::$sharedInstance;
        }

        $processor = new Processor();
        $config = $processor->processConfiguration(
            new DatabaseConfiguration(),
            ['database' => $rawConfig]
        );

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['name']
        );

        return new \PDO($dsn, $config['user'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
