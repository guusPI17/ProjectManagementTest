<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatusCode;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'ProjectStatus', description: 'Статус жизненного цикла проекта')]
class ProjectStatus
{
    #[OA\Property(description: 'ID статуса', example: 1)]
    #[Groups(['read'])]
    public ?int $id = null;

    #[OA\Property(description: 'Код статуса', example: 'production')]
    #[Groups(['read'])]
    #[Assert\NotBlank(message: 'Код статуса обязателен')]
    #[Assert\Length(max: 255)]
    #[Assert\Choice(callback: [ProjectStatusCode::class, 'values'], message: 'Недопустимый код статуса')]
    public ?string $code = null;

    #[OA\Property(description: 'Название статуса', example: 'Продакшен')]
    #[Groups(['read'])]
    #[Assert\NotBlank(message: 'Название статуса обязательно')]
    #[Assert\Length(max: 255)]
    public ?string $name = null;
}
