<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Filters\ProjectFilter;
use App\Models\PaginatedResult;
use App\Models\Project;
use App\Repositories\PlatformRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectStatusRepository;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly PlatformRepository $platformRepository,
        private readonly ProjectStatusRepository $statusRepository,
    ) {
    }

    public function getAll(ProjectFilter $filter): PaginatedResult
    {
        $total = $this->projectRepository->count($filter);
        $items = $this->projectRepository->findAll($filter);

        return new PaginatedResult($items, $total, $filter->page, $filter->perPage);
    }

    /**
     * @throws NotFoundException
     */
    public function getById(int $id): Project
    {
        $project = $this->projectRepository->findById($id);

        if ($project === null) {
            throw new NotFoundException("Проект с id {$id} не найден");
        }

        return $project;
    }

    public function create(Project $project): Project
    {
        $this->validateRelations($project->platformId, $project->statusId);

        $this->projectRepository->create($project);

        return $this->getById($project->id);
    }

    public function update(int $id, Project $project): Project
    {
        $this->validateRelations($project->platformId, $project->statusId);

        $this->projectRepository->update($id, $project);

        return $this->getById($id);
    }

    public function delete(int $id): void
    {
        if (!$this->projectRepository->delete($id)) {
            throw new NotFoundException("Проект с id {$id} не найден");
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateRelations(?int $platformId, ?int $statusId): void
    {
        if ($platformId !== null && $this->platformRepository->findById($platformId) === null) {
            throw new NotFoundException("Платформа с id {$platformId} не найдена");
        }

        if ($statusId !== null && $this->statusRepository->findById($statusId) === null) {
            throw new NotFoundException("Статус с id {$statusId} не найден");
        }
    }
}
