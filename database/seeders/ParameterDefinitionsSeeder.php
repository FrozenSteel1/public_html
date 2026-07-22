<?php

namespace Database\Seeders;

use App\Models\ParameterDefinition;
use Illuminate\Database\Seeder;

class ParameterDefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        $parameters = [
            'Институциональная устойчивость',
            'Управляемость аппарата',
            'Конфликтная напряженность',
            'Публичная легитимность',
            'Доверие к процедурам',
            'Риск управленческого сбоя',
            'Горизонт устойчивости',
        ];

        foreach ($parameters as $name) {
            ParameterDefinition::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }
    }
}
