<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;

class ClearGames extends Command
{
    protected $signature = 'games:clear {userId?}';
    protected $description = 'Очистить активные игры пользователя';

    public function handle()
    {
        $userId = $this->argument('userId');

        $query = Game::where('status', 'in_progress');

        if ($userId) {
            $query->where('user_id', $userId);
            $this->info("Очистка активных игр для пользователя ID: {$userId}");
        } else {
            $this->info("Очистка всех активных игр");
        }

        $count = $query->update(['status' => 'completed']);
        $this->info("Завершено игр: {$count}");
    }
}
