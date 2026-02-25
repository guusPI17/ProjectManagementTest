<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Filters\ProjectFilter;
use App\Models\Platform;
use App\Models\Project;
use App\Models\ProjectStatus;

class ProjectRepository
{
    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    public function findById(int $id): ?Project
    {
        $stmt = $this->pdo->prepare('
            SELECT p.id, p.name, p.url, p.platform_id, p.status_id, p.description,
                   p.created_at, p.updated_at,
                   pl.code AS platform_code, pl.name AS platform_name,
                   ps.code AS status_code,
                   ps.name AS status_name
            FROM projects p
            LEFT JOIN platforms pl ON pl.id = p.platform_id
            LEFT JOIN project_statuses ps ON ps.id = p.status_id
            WHERE p.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @return Project[]
     */
    public function findAll(ProjectFilter $filter): array
    {
        [$whereSql, $params] = $this->buildWhereClause($filter);

        $sql = '
            SELECT p.id, p.name, p.url, p.platform_id, p.status_id, p.description,
                   p.created_at, p.updated_at,
                   pl.code AS platform_code, pl.name AS platform_name,
                   ps.code AS status_code,
                   ps.name AS status_name
            FROM projects p
            LEFT JOIN platforms pl ON pl.id = p.platform_id
            LEFT JOIN project_statuses ps ON ps.id = p.status_id
        ' . $whereSql . ' ORDER BY p.id DESC LIMIT :limit OFFSET :offset';

        $params['limit'] = $filter->perPage;
        $params['offset'] = $filter->getOffset();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map(fn (array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    public function count(ProjectFilter $filter): int
    {
        [$whereSql, $params] = $this->buildWhereClause($filter);

        $sql = '
            SELECT COUNT(*)
            FROM projects p
            LEFT JOIN platforms pl ON pl.id = p.platform_id
            LEFT JOIN project_statuses ps ON ps.id = p.status_id
        ' . $whereSql;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array{string, array<string, mixed>}
     */
    private function buildWhereClause(ProjectFilter $filter): array
    {
        $where = [];
        $params = [];

        if ($filter->status !== null) {
            $where[] = 'ps.code = :status';
            $params['status'] = $filter->status;
        }

        if ($filter->platform !== null) {
            $where[] = 'pl.code = :platform';
            $params['platform'] = $filter->platform;
        }

        $sql = $where !== [] ? ' WHERE ' . implode(' AND ', $where) : '';

        return [$sql, $params];
    }

    public function create(Project $project): Project
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO projects (name, url, platform_id, status_id, description)
            VALUES (:name, :url, :platform_id, :status_id, :description)
            RETURNING id, created_at, updated_at
        ');

        $stmt->execute([
            'name' => $project->name,
            'url' => $project->url,
            'platform_id' => $project->platformId,
            'status_id' => $project->statusId,
            'description' => $project->description,
        ]);

        $row = $stmt->fetch();
        if ($row === false) {
            throw new \RuntimeException('Не удалось создать проект');
        }
        $project->id = (int) $row['id'];
        $project->createdAt = $row['created_at'];
        $project->updatedAt = $row['updated_at'];

        return $project;
    }

    public function update(int $id, Project $project): void
    {
        $sql = 'UPDATE projects SET name = :name, url = :url, platform_id = :platform_id,
                status_id = :status_id, description = :description WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'name' => $project->name,
            'url' => $project->url,
            'platform_id' => $project->platformId,
            'status_id' => $project->statusId,
            'description' => $project->description,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Project
    {
        $project = new Project();
        $project->id = (int) $row['id'];
        $project->name = $row['name'];
        $project->url = $row['url'];
        $project->platformId = $row['platform_id'] !== null ? (int) $row['platform_id'] : null;
        $project->statusId = $row['status_id'] !== null ? (int) $row['status_id'] : null;
        $project->description = $row['description'];

        if ($row['platform_code'] !== null) {
            $platform = new Platform();
            $platform->id = (int) $row['platform_id'];
            $platform->code = $row['platform_code'];
            $platform->name = $row['platform_name'];
            $project->platform = $platform;
        }

        if ($row['status_code'] !== null) {
            $status = new ProjectStatus();
            $status->id = (int) $row['status_id'];
            $status->code = $row['status_code'];
            $status->name = $row['status_name'];
            $project->status = $status;
        }
        $project->createdAt = $row['created_at'];
        $project->updatedAt = $row['updated_at'];

        return $project;
    }
}
