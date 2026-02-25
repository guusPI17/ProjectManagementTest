<?php

declare(strict_types=1);

namespace App\Actions\ProjectStatus;

use App\Filters\ProjectStatusFilter;
use App\Models\PaginatedResult;
use App\Repositories\ProjectStatusRepository;
use App\Responses\PaginatedResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/statuses', name: 'statuses_list', methods: ['GET'])]
#[OA\Get(
    path: '/api/statuses',
    summary: 'Список статусов проектов',
    tags: ['Project Statuses'],
    parameters: [
        new OA\Parameter(name: 'code', in: 'query', required: false, schema: new OA\Schema(type: 'string'), description: 'Фильтр по коду статуса'),
        new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1), description: 'Номер страницы'),
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15), description: 'Количество элементов на странице (1-100)'),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Список статусов с пагинацией',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProjectStatus')),
                new OA\Property(property: 'meta', properties: [
                    new OA\Property(property: 'current_page', type: 'integer', example: 1),
                    new OA\Property(property: 'per_page', type: 'integer', example: 15),
                    new OA\Property(property: 'total', type: 'integer', example: 4),
                    new OA\Property(property: 'last_page', type: 'integer', example: 1),
                ], type: 'object'),
            ])
        ),
    ]
)]
class ListStatusesAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ProjectStatusRepository $repository,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $filter = new ProjectStatusFilter($request);
        $total = $this->repository->count($filter);
        $items = $this->repository->findAllFiltered($filter);
        $result = new PaginatedResult($items, $total, $filter->page, $filter->perPage);

        return PaginatedResponse::fromResult($result, $this->serializer);
    }
}
