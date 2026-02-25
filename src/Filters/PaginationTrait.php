<?php

declare(strict_types=1);

namespace App\Filters;

use Symfony\Component\HttpFoundation\Request;

trait PaginationTrait
{
    public readonly int $page;
    public readonly int $perPage;

    protected function initPagination(Request $request): void
    {
        $this->page = max(1, $request->query->getInt('page', 1));
        $this->perPage = min(100, max(1, $request->query->getInt('per_page', 15)));
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
