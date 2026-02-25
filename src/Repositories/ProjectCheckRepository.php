<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ProjectCheck;

class ProjectCheckRepository
{
    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    /**
     * @return ProjectCheck[]
     */
    public function findByProjectId(int $projectId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT id, project_id, http_status_code, response_time_ms, is_available, message, checked_at
            FROM project_checks
            WHERE project_id = :project_id
            ORDER BY checked_at DESC
        ');
        $stmt->execute(['project_id' => $projectId]);

        return array_map(fn (array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    public function create(ProjectCheck $check): ProjectCheck
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO project_checks (project_id, http_status_code, response_time_ms, is_available, message)
            VALUES (:project_id, :http_status_code, :response_time_ms, :is_available, :message)
            RETURNING id, checked_at
        ');

        $stmt->bindValue('project_id', $check->projectId);
        $stmt->bindValue('http_status_code', $check->httpStatusCode);
        $stmt->bindValue('response_time_ms', $check->responseTimeMs);
        $stmt->bindValue('is_available', $check->isAvailable, \PDO::PARAM_BOOL);
        $stmt->bindValue('message', $check->message);
        $stmt->execute();

        $row = $stmt->fetch();
        if ($row === false) {
            throw new \RuntimeException('Не удалось создать результат проверки');
        }
        $check->id = (int) $row['id'];
        $check->checkedAt = $row['checked_at'];

        return $check;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): ProjectCheck
    {
        $check = new ProjectCheck();
        $check->id = (int) $row['id'];
        $check->projectId = (int) $row['project_id'];
        $check->httpStatusCode = $row['http_status_code'] !== null ? (int) $row['http_status_code'] : null;
        $check->responseTimeMs = (int) $row['response_time_ms'];
        $check->isAvailable = (bool) $row['is_available'];
        $check->message = $row['message'];
        $check->checkedAt = $row['checked_at'];

        return $check;
    }
}
