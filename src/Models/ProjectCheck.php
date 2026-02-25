<?php

declare(strict_types=1);

namespace App\Models;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'ProjectCheck', description: 'Результат проверки доступности сайта')]
class ProjectCheck
{
    #[OA\Property(description: 'ID проверки', example: 1)]
    #[Groups(['read'])]
    public ?int $id = null;

    #[OA\Property(description: 'ID проекта', example: 1)]
    #[Groups(['read'])]
    #[Assert\NotNull(message: 'ID проекта обязателен')]
    #[Assert\Positive]
    public ?int $projectId = null;

    #[OA\Property(description: 'HTTP статус код ответа', example: 200)]
    #[Groups(['read'])]
    #[Assert\Range(min: 100, max: 599, notInRangeMessage: 'HTTP код должен быть от 100 до 599')]
    public ?int $httpStatusCode = null;

    #[OA\Property(description: 'Время ответа в миллисекундах', example: 150)]
    #[Groups(['read'])]
    #[Assert\NotNull(message: 'Время ответа обязательно')]
    #[Assert\PositiveOrZero]
    public ?int $responseTimeMs = null;

    #[OA\Property(description: 'Флаг доступности', example: true)]
    #[Groups(['read'])]
    public bool $isAvailable = false;

    #[OA\Property(description: 'Сообщение', example: 'Connection timeout')]
    #[Groups(['read'])]
    #[Assert\Length(max: 2000)]
    public ?string $message = null;

    #[OA\Property(description: 'Дата и время проверки', example: '2026-02-25 12:00:00')]
    #[Groups(['read'])]
    public ?string $checkedAt = null;
}
