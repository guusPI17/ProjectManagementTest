<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Phinx\Migration\AbstractMigration;

final class CreatePlatformsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('platforms', ['comment' => 'Справочник платформ проектов']);
        $table
            ->addColumn('code', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Код платформы',
            ])
            ->addColumn('name', 'string', [
                'limit' => 255,
                'null' => false,
                'comment' => 'Название платформы',
            ])
            ->addIndex(['code'], ['unique' => true])
            ->create();

        $table->insert([
            ['code' => 'wordpress', 'name' => 'WordPress'],
            ['code' => 'bitrix', 'name' => 'Битрикс'],
            ['code' => 'custom', 'name' => 'Кастомная'],
            ['code' => 'other', 'name' => 'Прочее'],
        ])->saveData();
    }
}
