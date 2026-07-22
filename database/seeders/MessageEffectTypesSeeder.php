<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageEffectTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Сообщение',
                'description' => 'Показывает сообщение игроку. Данные: {"message": "Текст сообщения", "type": "info|warning|success|error"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Отложенное сообщение',
                'description' => 'Показывает сообщение игроку через указанное количество ходов. Данные: {"message": "Текст", "delay": 2, "type": "info"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($types as $type) {
            $exists = DB::table('effect_types')
                ->where('name', $type['name'])
                ->exists();

            if (!$exists) {
                DB::table('effect_types')->insert($type);
                $this->command->info("Тип эффекта '{$type['name']}' создан.");
            } else {
                $this->command->info("Тип эффекта '{$type['name']}' уже существует. Пропускаем.");
            }
        }
    }
}
