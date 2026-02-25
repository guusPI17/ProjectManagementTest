<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatusCode: string
{
    use BaseEnumTrait;

    case Development = 'development';
    case Production = 'production';
    case Maintenance = 'maintenance';
    case Archived = 'archived';
}
