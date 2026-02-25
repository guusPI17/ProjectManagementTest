<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Services\ProjectCheckerService;
use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/projects/{id}/check', name: 'projects_check', requirements: ['id' => '\d+'], methods: ['POST'])]
#[OA\Post(
    path: '/api/projects/{id}/check',
    summary: 'Проверить доступность сайта проекта',
    tags: ['Projects'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(
            response: 201,
            description: 'Результат проверки',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ProjectCheck'),
            ])
        ),
        new OA\Response(response: 404, description: 'Проект не найден'),
    ]
)]
class CheckProjectAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ProjectService $projectService,
        private readonly ProjectCheckerService $checkerService,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $project = $this->projectService->getById($id);
        $check = $this->checkerService->check($project->id, $project->url);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(['data' => $check], 'json', ['groups' => ['read']]),
            Response::HTTP_CREATED
        );
    }
}
