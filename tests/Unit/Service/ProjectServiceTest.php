<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exceptions\NotFoundException;
use App\Filters\ProjectFilter;
use App\Models\PaginatedResult;
use App\Models\Platform;
use App\Models\Project;
use App\Repositories\PlatformRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectStatusRepository;
use App\Services\ProjectService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ProjectServiceTest extends TestCase
{
    private ProjectService $service;
    private ProjectRepository $projectRepo;
    private PlatformRepository $platformRepo;
    private ProjectStatusRepository $statusRepo;

    protected function setUp(): void
    {
        $this->projectRepo = $this->createMock(ProjectRepository::class);
        $this->platformRepo = $this->createMock(PlatformRepository::class);
        $this->statusRepo = $this->createMock(ProjectStatusRepository::class);

        $this->service = new ProjectService(
            $this->projectRepo,
            $this->platformRepo,
            $this->statusRepo,
        );
    }

    public function testGetByIdReturnsProject(): void
    {
        $project = new Project();
        $project->id = 1;
        $project->name = 'Test';

        $this->projectRepo->method('findById')->with(1)->willReturn($project);

        $result = $this->service->getById(1);

        $this->assertSame(1, $result->id);
        $this->assertSame('Test', $result->name);
    }

    public function testGetByIdThrowsNotFound(): void
    {
        $this->projectRepo->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->service->getById(PHP_INT_MAX);
    }

    public function testGetAllReturnsPaginatedResult(): void
    {
        $p1 = new Project();
        $p1->id = 1;
        $p2 = new Project();
        $p2->id = 2;

        $filter = new ProjectFilter(new Request());
        $this->projectRepo->method('findAll')->willReturn([$p1, $p2]);
        $this->projectRepo->method('count')->willReturn(2);

        $result = $this->service->getAll($filter);

        $this->assertInstanceOf(PaginatedResult::class, $result);
        $this->assertCount(2, $result->items);
        $this->assertSame(2, $result->total);
        $this->assertSame(1, $result->page);
        $this->assertSame(15, $result->perPage);
        $this->assertSame(1, $result->lastPage());
    }

    public function testDeleteThrowsNotFoundForMissingProject(): void
    {
        $this->projectRepo->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->service->delete(PHP_INT_MAX);
    }

    public function testCreateValidatesRelations(): void
    {
        $project = new Project();
        $project->platformId = PHP_INT_MAX;
        $project->statusId = 1;

        $this->platformRepo->method('findById')->with(PHP_INT_MAX)->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Платформа с id ' . PHP_INT_MAX . ' не найдена');
        $this->service->create($project);
    }

    public function testCreateValidatesStatusRelation(): void
    {
        $platform = new Platform();
        $platform->id = 1;

        $project = new Project();
        $project->platformId = 1;
        $project->statusId = PHP_INT_MAX;

        $this->platformRepo->method('findById')->with(1)->willReturn($platform);
        $this->statusRepo->method('findById')->with(PHP_INT_MAX)->willReturn(null);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Статус с id ' . PHP_INT_MAX . ' не найден');
        $this->service->create($project);
    }
}
