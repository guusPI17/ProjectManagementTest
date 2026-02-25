<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Exceptions\ValidationException;
use App\Models\Project;
use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/projects/{id}', name: 'projects_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
#[OA\Put(
    path: '/api/projects/{id}',
    summary: 'Обновить проект',
    tags: ['Projects'],
    parameters: [
        new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'My Website'),
                new OA\Property(property: 'url', type: 'string', example: 'https://example.com'),
                new OA\Property(property: 'platform_id', type: 'integer', example: 1),
                new OA\Property(property: 'status_id', type: 'integer', example: 2),
                new OA\Property(property: 'description', type: 'string', example: 'Основной сайт компании', nullable: true),
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Проект обновлён',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Project'),
            ])
        ),
        new OA\Response(response: 400, description: 'Ошибка валидации'),
        new OA\Response(response: 404, description: 'Проект не найден'),
    ]
)]
class UpdateProjectAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly ProjectService $service,
    ) {
    }

    public function __invoke(Request $request, int $id): JsonResponse
    {
        $existing = $this->service->getById($id);

        $project = $this->serializer->deserialize(
            $request->getContent(),
            Project::class,
            'json',
            ['groups' => ['update'], 'object_to_populate' => $existing]
        );

        $violations = $this->validator->validate($project, null, ['update']);
        if (count($violations) > 0) {
            throw ValidationException::fromViolations($violations);
        }

        $updated = $this->service->update($id, $project);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(['data' => $updated], 'json', ['groups' => ['read']])
        );
    }
}
