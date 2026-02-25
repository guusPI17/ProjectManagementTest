<?php

declare(strict_types=1);

namespace App\Models;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'Project', description: 'Проект компании')]
class Project
{
    #[OA\Property(description: 'ID проекта', example: 1)]
    #[Groups(['read'])]
    public ?int $id = null;

    #[OA\Property(description: 'Название проекта', example: 'My Website')]
    #[Groups(['read', 'create', 'update'])]
    #[Assert\NotBlank(message: 'Название проекта обязательно', groups: ['create', 'update'])]
    #[Assert\Length(max: 255, groups: ['create', 'update'])]
    public ?string $name = null;

    #[OA\Property(description: 'URL адрес сайта проекта', example: 'https://example.com')]
    #[Groups(['read', 'create', 'update'])]
    #[Assert\NotBlank(message: 'URL обязателен', groups: ['create', 'update'])]
    #[Assert\Url(message: 'URL должен быть валидным', groups: ['create', 'update'])]
    #[Assert\Length(max: 2048, groups: ['create', 'update'])]
    public ?string $url = null;

    #[OA\Property(description: 'ID платформы', example: 1)]
    #[Groups(['read', 'create', 'update'])]
    #[Assert\NotBlank(message: 'Платформа обязательна', groups: ['create', 'update'])]
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $platformId = null;

    #[OA\Property(description: 'ID статуса проекта', example: 2)]
    #[Groups(['read', 'create', 'update'])]
    #[Assert\NotBlank(message: 'Статус обязателен', groups: ['create', 'update'])]
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $statusId = null;

    #[OA\Property(description: 'Описание проекта', example: 'Основной сайт компании')]
    #[Groups(['read', 'create', 'update'])]
    #[Assert\Length(max: 2000, groups: ['create', 'update'])]
    public ?string $description = null;

    #[OA\Property(description: 'Платформа проекта')]
    #[Groups(['read'])]
    public ?Platform $platform = null;

    #[OA\Property(description: 'Статус проекта')]
    #[Groups(['read'])]
    public ?ProjectStatus $status = null;

    #[OA\Property(description: 'Дата создания', example: '2026-02-25 12:00:00')]
    #[Groups(['read'])]
    public ?string $createdAt = null;

    #[OA\Property(description: 'Дата последнего обновления', example: '2026-02-25 12:00:00')]
    #[Groups(['read'])]
    public ?string $updatedAt = null;
}
