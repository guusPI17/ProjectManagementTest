<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Phinx\Migration\AbstractMigration;

final class CreateProjectsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('projects', ['comment' => 'Проекты (сайты) компании']);
        $table
            ->addColumn('name', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Название проекта',
            ])
            ->addColumn('url', 'string', [
                'limit' => 2048,
                'null' => false,
                'comment' => 'URL адрес сайта проекта',
            ])
            ->addColumn('platform_id', 'integer', [
                'null' => false,
                'signed' => true,
                'comment' => 'Ссылка на платформу (platforms.id)',
            ])
            ->addColumn('status_id', 'integer', [
                'null' => false,
                'signed' => true,
                'comment' => 'Ссылка на статус проекта (project_statuses.id)',
            ])
            ->addColumn('description', 'text', [
                'null' => true,
                'comment' => 'Описание проекта (опционально)',
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'comment' => 'Дата и время создания записи',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'comment' => 'Дата и время последнего обновления',
            ])
            ->addIndex(['status_id'])
            ->addIndex(['platform_id'])
            ->addForeignKey('platform_id', 'platforms', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
            ])
            ->addForeignKey('status_id', 'project_statuses', 'id', [
                'delete' => 'RESTRICT',
                'update' => 'CASCADE',
            ])
            ->create();

        $this->execute("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS \$\$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;

            COMMENT ON FUNCTION update_updated_at_column()
                IS 'Автоматически обновляет поле updated_at при изменении записи';

            CREATE TRIGGER set_updated_at
                BEFORE UPDATE ON projects
                FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
        ");
    }
}
