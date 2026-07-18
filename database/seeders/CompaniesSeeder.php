<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        // Проверяем, есть ли уже компании
        if (Company::count() > 0) {
            $this->command->info('Компании уже существуют. Пропускаем.');
            return;
        }

        Company::create([
            'name' => 'Администрация округа',
            'description' => 'Основная компания для игры',
            'difficulty' => 'easy',
        ]);

        $this->command->info('Компания создана.');
    }
}
