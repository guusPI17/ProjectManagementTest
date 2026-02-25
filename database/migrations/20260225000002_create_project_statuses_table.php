<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Phinx\Migration\AbstractMigration;

final class CreateProjectStatusesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('project_statuses', ['comment' => 'Справочник статусов жизненного цикла проекта']);
        $table
            ->addColumn('code', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Код статуса',
            ])
            ->addColumn('name', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Название статуса',
            ])
            ->addIndex(['code'], ['unique' => true])
            ->create();

        $table->insert([
            ['code' => 'development', 'name' => 'Разработка'],
            ['code' => 'production', 'name' => 'Продакшен'],
            ['code' => 'maintenance', 'name' => 'Поддержка'],
            ['code' => 'archived', 'name' => 'Архив'],
        ])->saveData();
    }
}
