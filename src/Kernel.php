<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\HttpException;
use App\Exceptions\ValidationException;
use App\Responses\ErrorResponse;
use App\Routing\ActionRouteLoader;
use App\Routing\ArgumentResolver;
use OpenApi\Attributes as OA;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Info(title: 'Project Management API', version: '1.0.0', description: 'REST API для управления проектами компании')]
#[OA\Tag(name: 'Projects', description: 'Управление проектами')]
#[OA\Tag(name: 'Platforms', description: 'Платформы проектов')]
#[OA\Tag(name: 'Project Statuses', description: 'Статусы проектов')]
class Kernel
{
    private ContainerInterface $container;
    private RouteCollection $routes;

    public function __construct()
    {
        $this->container = require __DIR__ . '/../config/services.php';

        $locator = new FileLocator();
        $dirLoader = new AttributeDirectoryLoader($locator, new ActionRouteLoader());
        $this->routes = $dirLoader->load(__DIR__ . '/Actions');
    }

    public function handle(Request $request): JsonResponse
    {
        try {
            $context = new RequestContext();
            $context->fromRequest($request);

            $matcher = new UrlMatcher($this->routes, $context);
            $parameters = $matcher->match($request->getPathInfo());

            $actionClass = $parameters['_action'];
            $action = $this->container->get($actionClass);

            $resolver = new ArgumentResolver();
            $args = $resolver->resolve($action, $request, $parameters);

            return $action->__invoke(...$args);
        } catch (ResourceNotFoundException) {
            return (new ErrorResponse('Не найдено'))->toJsonResponse(Response::HTTP_NOT_FOUND);
        } catch (MethodNotAllowedException) {
            return (new ErrorResponse('Метод не разрешён'))->toJsonResponse(Response::HTTP_METHOD_NOT_ALLOWED);
        } catch (ValidationException $e) {
            return (new ErrorResponse($e->getMessage(), $e->getErrors()))->toJsonResponse(Response::HTTP_BAD_REQUEST);
        } catch (HttpException $e) {
            return (new ErrorResponse($e->getMessage()))->toJsonResponse($e->getStatusCode());
        } catch (\Throwable $e) {
            $message = ($_ENV['APP_ENV'] ?? 'prod') === 'dev' ? $e->getMessage() : 'Внутренняя ошибка сервера';

            return (new ErrorResponse($message))->toJsonResponse(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public static function createSerializer(): Serializer
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizer = new ObjectNormalizer(
            $classMetadataFactory,
            new CamelCaseToSnakeCaseNameConverter(),
        );

        return new Serializer([$normalizer], [new JsonEncoder()]);
    }

    public static function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }
}
