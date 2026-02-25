<?php

declare(strict_types=1);

$cacheDir = __DIR__ . '/../var/cache';

if (!is_dir($cacheDir)) {
    echo "Директория кеша не найдена." . PHP_EOL;
    exit(0);
}

$files = glob($cacheDir . '/*');
$count = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        $count++;
    }
}

echo "Кеш очищен. Удалено файлов: {$count}." . PHP_EOL;
