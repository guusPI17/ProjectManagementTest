<?php

declare(strict_types=1);

namespace App\Routing;

use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\Route;

/**
 * Загрузчик маршрутов из #[Route] атрибутов на invokable Action-классах.
 *
 * Сканирует классы в директории src/Actions/ через AttributeDirectoryLoader,
 * читает атрибуты #[Route], который затем используется в Kernel для получения сервиса из DI-контейнера.
 */
class ActionRouteLoader extends AttributeClassLoader
{
    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $attr): void
    {
        $route->setDefault('_action', $class->getName());
    }
}
