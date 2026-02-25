<?php

declare(strict_types=1);

namespace App\Enums;

enum PlatformCode: string
{
    use BaseEnumTrait;

    case Wordpress = 'wordpress';
    case Bitrix = 'bitrix';
    case Custom = 'custom';
    case Other = 'other';
}
