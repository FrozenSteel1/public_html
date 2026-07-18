<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Scenario;
use App\Services\GameService;
use Illuminate\Console\Command;

class TestGame extends Command
{
    protected $signature = 'game:test {scenarioId?} {userId?}';
    protected $description = 'Тестирование игрового движка';

    public function handle(GameService $gameService)
    {
        // Получаем пользователя
        $userId = $this->argument('userId') ?? User::first()?->id;
        if (!$userId) {
            $this->error('Пользователь не найден. Создайте пользователя или укажите ID.');
            return 1;
        }

        // Получаем сценарий
        $scenarioId = $this->argument('scenarioId') ?? Scenario::first()?->id;
        if (!$scenarioId) {
            $this->error('Сценарий не найден. Создайте сценарий или укажите ID.');
            return 1;
        }

        $scenario = Scenario::find($scenarioId);
        $this->info("Тестирование сценария: {$scenario->name}");

        // Получаем доступные сложности
        $difficulties = $scenario->presets->pluck('difficulty')->toArray();
        if (empty($difficulties)) {
            $this->error('Для сценария нет пресетов.');
            return 1;
        }

        $difficulty = $difficulties[0];
        $this->info("Сложность: {$difficulty}");

        try {
            // Стартуем игру
            $game = $gameService->startGame($userId, $scenarioId, $difficulty);
            $this->info("Игра создана. ID: {$game->id}");

            // Получаем первую сцену
            $scene = $gameService->getCurrentScene($game);
            $this->info("Текущая сцена: {$scene->title}");

            // Получаем доступные выборы
            $choices = $gameService->getAvailableChoices($game);
            $this->info("Доступно выборов: " . count($choices));

            // Выводим состояние
            $state = $game->getCurrentState();
            $this->info("Текущее состояние:");
            foreach ($state as $key => $value) {
                $this->line("  {$key}: {$value}");
            }

            // Если есть выборы - делаем первый
            if (!empty($choices)) {
                $choice = $choices[0];
                $this->info("Выполняем выбор: {$choice->description}");

                $result = $gameService->makeChoice($game->id, $choice->id);

                $this->info("Новое состояние:");
                foreach ($result['new_state'] as $key => $value) {
                    $this->line("  {$key}: {$value}");
                }

                if (!empty($result['triggered_events'])) {
                    $this->info("Сработали триггеры акторов:");
                    foreach ($result['triggered_events'] as $trigger) {
                        $this->line("  {$trigger['actor_name']}: {$trigger['event_name']}");
                    }
                }

                if ($result['next_scene']) {
                    $this->info("Следующая сцена: {$result['next_scene']->title}");
                } else {
                    $this->info("Игра завершена!");
                }
            }

            $this->info("Тестирование завершено успешно!");

        } catch (\Exception $e) {
            $this->error("Ошибка: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
