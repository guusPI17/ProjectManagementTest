<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Kernel;

abstract class AppTestCase extends DatabaseTestCase
{
    protected Kernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kernel = new Kernel();
    }

    protected function createTestProject(): int
    {
        $stmt = self::$pdo->prepare('
            INSERT INTO projects (name, url, platform_id, status_id, description)
            VALUES (:name, :url, :platform_id, :status_id, :description)
            RETURNING id
        ');

        $stmt->execute([
            'name' => 'Test Project',
            'url' => 'https://example.com',
            'platform_id' => 1,
            'status_id' => 1,
            'description' => 'Test',
        ]);

        return (int) $stmt->fetchColumn();
    }
}
