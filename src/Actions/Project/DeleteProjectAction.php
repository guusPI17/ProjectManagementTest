<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Responses\MessageResponse;
use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/projects/{id}', name: 'projects_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
#[OA\Delete(
    path: '/api/projects/{id}',
    summary: 'Удалить проект',
    tags: ['Projects'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Проект удалён',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Проект успешно удалён'),
            ])
        ),
        new OA\Response(response: 404, description: 'Проект не найден'),
    ]
)]
class DeleteProjectAction
{
    public function __construct(
        private readonly ProjectService $service,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        $this->service->delete($id);

        return (new MessageResponse('Проект успешно удалён'))->toJsonResponse();
    }
}
