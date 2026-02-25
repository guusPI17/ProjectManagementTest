<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Filters\PlatformFilter;
use App\Models\Platform;

class PlatformRepository
{
    public function __construct(
        private readonly \PDO $pdo,
    ) {
    }

    public function findById(int $id): ?Platform
    {
        $stmt = $this->pdo->prepare('SELECT id, code, name FROM platforms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @return Platform[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT id, code, name FROM platforms ORDER BY id');

        return array_map(fn (array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    /**
     * @return Platform[]
     */
    public function findAllFiltered(PlatformFilter $filter): array
    {
        $sql = 'SELECT id, code, name FROM platforms';
        $params = [];

        if ($filter->code !== null) {
            $sql .= ' WHERE code = :code';
            $params['code'] = $filter->code;
        }

        $sql .= ' ORDER BY id LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue('limit', $filter->perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $filter->getOffset(), \PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn (array $row) => $this->hydrate($row), $stmt->fetchAll());
    }

    public function count(PlatformFilter $filter): int
    {
        $sql = 'SELECT COUNT(*) FROM platforms';
        $params = [];

        if ($filter->code !== null) {
            $sql .= ' WHERE code = :code';
            $params['code'] = $filter->code;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): Platform
    {
        $platform = new Platform();
        $platform->id = (int) $row['id'];
        $platform->code = $row['code'];
        $platform->name = $row['name'];

        return $platform;
    }
}
