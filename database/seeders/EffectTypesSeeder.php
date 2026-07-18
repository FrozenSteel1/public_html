<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EffectTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Проверяем, существует ли уже такой тип
        $existing = DB::table('effect_types')
            ->where('name', 'Смена сцены')
            ->first();

        if ($existing) {
            $this->command->info('Тип эффекта "Смена сцены" уже существует. Пропускаем.');
            return;
        }

        // Добавляем новый тип эффекта
        $id = DB::table('effect_types')->insertGetId([
            'name' => 'Смена сцены',
            'description' => 'Переход к указанной сцене. ID целевой сцены указывается в effect_data в формате {"target_scene_id": 123}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("Тип эффекта 'Смена сцены' успешно создан с ID: {$id}");

        // Выводим для справки ID, чтобы использовать в коде
        $this->command->warn("ID для использования в коде: {$id}");
        $this->command->warn("Используйте: EffectType::SCENE_TRANSITION = {$id}");
    }
}
