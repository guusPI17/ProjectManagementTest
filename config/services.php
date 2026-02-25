<?php

declare(strict_types=1);

use App\Database\Connection;
use App\Kernel;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

$isDebug = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
$cacheFile = __DIR__ . '/../var/cache/container.php';
$containerConfigCache = new ConfigCache($cacheFile, $isDebug);

if ($containerConfigCache->isFresh()) {
    require_once $cacheFile;

    if (class_exists('ProjectContainer', false)) {
        return new \ProjectContainer();
    }
}

$container = new ContainerBuilder();

// Сервисы-фабрики
$container->register(\PDO::class, \PDO::class)
    ->setFactory([Connection::class, 'createFromConfig'])
    ->addArgument(require __DIR__ . '/database.php')
    ->setPublic(true);

$container->register(SerializerInterface::class, SerializerInterface::class)
    ->setFactory([Kernel::class, 'createSerializer'])
    ->setPublic(true);

$container->register(ValidatorInterface::class, ValidatorInterface::class)
    ->setFactory([Kernel::class, 'createValidator'])
    ->setPublic(true);

$container->register(HttpClientInterface::class, HttpClientInterface::class)
    ->setFactory([HttpClient::class, 'create'])
    ->setPublic(true);

// Авто-регистрация классов
$loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/..'));
$loader->registerClasses(
    (new Definition())->setAutowired(true)->setAutoconfigured(true)->setPublic(true),
    'App\\',
    'src/*',
    'src/{Kernel.php,Database/Connection.php,Models/*,Enums/*,Exceptions/*,Responses/*,Filters/*,Routing/*}',
);

$container->compile();

// Сохранить скомпилированный контейнер с отслеживанием ресурсов
$dumper = new PhpDumper($container);
$containerConfigCache->write(
    $dumper->dump(['class' => 'ProjectContainer']),
    $container->getResources(),
);

return $container;
