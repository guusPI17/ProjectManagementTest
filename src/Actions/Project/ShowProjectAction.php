<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/projects/{id}', name: 'projects_show', requirements: ['id' => '\d+'], methods: ['GET'])]
#[OA\Get(
    path: '/api/projects/{id}',
    summary: 'Получить проект по ID',
    tags: ['Projects'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Данные проекта',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Project'),
            ])
        ),
        new OA\Response(response: 404, description: 'Проект не найден'),
    ]
)]
class ShowProjectAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ProjectService $service,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $project = $this->service->getById($id);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(['data' => $project], 'json', ['groups' => ['read']])
        );
    }
}
