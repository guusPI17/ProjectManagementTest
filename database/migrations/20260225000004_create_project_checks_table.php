<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Phinx\Migration\AbstractMigration;

final class CreateProjectChecksTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('project_checks', ['comment' => 'Результаты проверок доступности сайтов проектов']);
        $table
            ->addColumn('project_id', 'integer', [
                'null' => false,
                'signed' => true,
                'comment' => 'Ссылка на проект (projects.id)',
            ])
            ->addColumn('http_status_code', 'integer', [
                'null' => true,
                'comment' => 'HTTP статус код ответа (null если сервер не ответил)',
            ])
            ->addColumn('response_time_ms', 'integer', [
                'null' => false,
                'comment' => 'Время ответа в миллисекундах',
            ])
            ->addColumn('is_available', 'boolean', [
                'default' => false,
                'null' => false,
                'comment' => 'Флаг доступности (true если HTTP 2xx/3xx)',
            ])
            ->addColumn('message', 'text', [
                'null' => true,
                'comment' => 'Сообщение',
            ])
            ->addColumn('checked_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'null' => false,
                'comment' => 'Дата и время проведения проверки',
            ])
            ->addIndex(['project_id'])
            ->addForeignKey('project_id', 'projects', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
            ])
            ->create();
    }
}
