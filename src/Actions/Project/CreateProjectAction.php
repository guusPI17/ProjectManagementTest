<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Exceptions\ValidationException;
use App\Models\Project;
use App\Services\ProjectService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/projects', name: 'projects_create', methods: ['POST'])]
#[OA\Post(
    path: '/api/projects',
    summary: 'Создать проект',
    tags: ['Projects'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'url', 'platform_id', 'status_id'],
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
            response: 201,
            description: 'Проект создан',
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/Project'),
            ])
        ),
        new OA\Response(response: 400, description: 'Ошибка валидации'),
    ]
)]
class CreateProjectAction
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly ProjectService $service,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $project = $this->serializer->deserialize(
            $request->getContent(),
            Project::class,
            'json',
            ['groups' => ['create']]
        );

        $violations = $this->validator->validate($project, null, ['create']);
        if (count($violations) > 0) {
            throw ValidationException::fromViolations($violations);
        }

        $created = $this->service->create($project);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(['data' => $created], 'json', ['groups' => ['read']]),
            Response::HTTP_CREATED
        );
    }
}
