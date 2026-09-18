<?php

namespace Database\Seeders;

use App\Models\ChoiceType;
use Illuminate\Database\Seeder;

class ChoiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Имена совпадают с именами событий 103–114 (события выборов игрока) —
        // это потребуется для автоподстановки типа на шагах 5–6.
        $types = [
            'Публичная позиция',
            'Процедурная коррекция',
            'Кулуарное согласование',
            'Компромисс',
            'Отсрочка / пауза',
            'Жёсткое продавливание',
            'Формальная фиксация',
            'Снятие ответственности',
            'Защита институциональных границ',
            'Стратегическое резервирование',
            'Решение через ресурсы',
            'Отсутствие решения',
        ];

        foreach ($types as $name) {
            ChoiceType::updateOrCreate(['name' => $name]);
        }
    }
}
