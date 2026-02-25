<?php

declare(strict_types=1);

namespace App\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Определяет аргументы для вызова __invoke() у Action-классов.
 *
 * Через Reflection анализирует сигнатуру метода и сопоставляет параметры
 * по типу: Request подставляется из HTTP-запроса, int — из параметров маршрута по имени.
 */
class ArgumentResolver
{
    /**
     * @param array<string, mixed> $routeParams
     *
     * @return array<mixed>
     */
    public function resolve(object $action, Request $request, array $routeParams): array
    {
        $method = new \ReflectionMethod($action, '__invoke');
        $args = [];

        foreach ($method->getParameters() as $param) {
            $typeName = $param->getType()?->getName();

            if ($typeName === Request::class) {
                $args[] = $request;
            } elseif ($typeName === 'int' && isset($routeParams[$param->getName()])) {
                $args[] = (int) $routeParams[$param->getName()];
            }
        }

        return $args;
    }
}
