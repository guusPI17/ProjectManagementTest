<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->loadEnv(__DIR__ . '/../.env');

$dbConfig = require __DIR__ . '/../config/database.php';

$dbName = $dbConfig['name'];
$host = $dbConfig['host'];
$port = $dbConfig['port'];
$user = $dbConfig['user'];
$password = $dbConfig['password'];
$driver = $dbConfig['driver'];

$dsn = sprintf('%s:host=%s;port=%d;dbname=postgres', $driver, $host, $port);

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $exists = $pdo->query(
        sprintf("SELECT 1 FROM pg_database WHERE datname = %s", $pdo->quote($dbName))
    )->fetchColumn();

    if ($exists) {
        echo "База данных '{$dbName}' уже существует." . PHP_EOL;
        exit(0);
    }

    $pdo->exec(sprintf('CREATE DATABASE "%s"', $dbName));
    echo "База данных '{$dbName}' создана успешно." . PHP_EOL;
} catch (PDOException $e) {
    fprintf(STDERR, "Не удалось создать базу данных: %s\n", $e->getMessage());
    exit(1);
}
