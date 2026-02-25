<?php

declare(strict_types=1);

namespace App\Filters;

use Symfony\Component\HttpFoundation\Request;

class ProjectStatusFilter
{
    use PaginationTrait;

    public readonly ?string $code;

    public function __construct(Request $request)
    {
        $this->code = $request->query->get('code');
        $this->initPagination($request);
    }
}
