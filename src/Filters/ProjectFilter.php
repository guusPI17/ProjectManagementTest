<?php

declare(strict_types=1);

namespace App\Filters;

use Symfony\Component\HttpFoundation\Request;

class ProjectFilter
{
    use PaginationTrait;

    public readonly ?string $status;
    public readonly ?string $platform;

    public function __construct(Request $request)
    {
        $this->status = $request->query->get('status');
        $this->platform = $request->query->get('platform');
        $this->initPagination($request);
    }
}
