<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlatformCode;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'Platform', description: 'Платформа проекта')]
class Platform
{
    #[OA\Property(description: 'ID платформы', example: 1)]
    #[Groups(['read'])]
    public ?int $id = null;

    #[OA\Property(description: 'Код платформы', example: 'wordpress')]
    #[Groups(['read'])]
    #[Assert\NotBlank(message: 'Код платформы обязателен')]
    #[Assert\Length(max: 255)]
    #[Assert\Choice(callback: [PlatformCode::class, 'values'], message: 'Недопустимый код платформы')]
    public ?string $code = null;

    #[OA\Property(description: 'Название платформы', example: 'WordPress')]
    #[Groups(['read'])]
    #[Assert\NotBlank(message: 'Название платформы обязательно')]
    #[Assert\Length(max: 255)]
    public ?string $name = null;
}
