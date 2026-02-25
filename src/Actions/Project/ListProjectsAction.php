<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Filters\ProjectFilter;
use App\Responses\PaginatedResponse;
use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/projects', name: 'projects_list', methods: ['GET'])]
#[OA\Get(
    path: '/api/projects',
    summary: 'Список проектов',
    tags: ['Projects'],
    parameters: [
        new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Фильтр по статусу'),
        new OA\Parameter(name: 'platform', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Фильтр по платформе'),
        new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1), description: 'Номер страницы'),
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15), description: 'Количество элементов на странице (1-100)'),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Список проектов с пагинацией',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Project')),
                new OA\Property(property: 'meta', properties: [
                    new OA\Property(property: 'current_page', type: 'integer', example: 1),
                    new OA\Property(property: 'per_page', type: 'integer', example: 15),
                    new OA\Property(property: 'total', type: 'integer', example: 50),
                    new OA\Property(property: 'last_page', type: 'integer', example: 4),
                ], type: 'object'),
            ])
        ),
    ]
)]
class ListProjectsAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ProjectService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $filter = new ProjectFilter($request);
        $result = $this->service->getAll($filter);

        return PaginatedResponse::fromResult($result, $this->serializer);
    }
}
