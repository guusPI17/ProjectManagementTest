<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Database\Connection;
use PHPUnit\Framework\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    protected static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = Connection::createFromConfig(require __DIR__ . '/../../config/database.php');
        Connection::setSharedInstance(self::$pdo);
    }

    public static function tearDownAfterClass(): void
    {
        Connection::setSharedInstance(null);
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        if (self::$pdo->inTransaction()) {
            self::$pdo->rollBack();
        }
    }
}
